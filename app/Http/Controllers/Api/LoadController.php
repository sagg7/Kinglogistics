<?php

namespace App\Http\Controllers\Api;

use App\Enums\AppConfigEnum;
use App\Enums\LoadStatusEnum;
use App\Exceptions\ShiftNotActiveException;
use App\Http\Controllers\Controller;
use App\Http\Resources\Drivers\LoadResource;
use App\Http\Resources\Drivers\LoadStatusResource;
use App\Http\Resources\Helpers\KeyValueResource;
use App\Jobs\BotLoadReminder;
use App\Models\AppConfig;
use App\Models\AvailableDriver;
use App\Models\BotAnswers;
use App\Models\Broker;
use App\Models\DispatchSchedule;
use App\Models\Driver;
use App\Models\Load;
use App\Models\LoadLog;
use App\Models\LoadStatus;
use App\Models\RejectedLoad;
use App\Traits\Load\GenerateLoads;
use App\Traits\Load\ManageLoadProcessTrait;
use App\Models\Trip;
use App\Traits\Shift\ShiftTrait;
use App\Traits\Storage\FileUpload;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\Accounting\PaymentsAndCollection;


class LoadController extends Controller
{

    use ShiftTrait, FileUpload, PaymentsAndCollection, ManageLoadProcessTrait, GenerateLoads;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getRecords()
    {
        $driver = auth()->user();

        $availableLoads = $driver->loads
            ->where('status', LoadStatusEnum::FINISHED)
            ->sortByDesc('id')->take(40);

        $loads = LoadResource::collection($availableLoads);

        return response($loads, 200);
    }

    public function storeLoad(Request $request)
    {
        $driver = auth()->user();

        if ($driver->hasActiveLoads()) {
            return response([
                'status' => 'error',
                'message' => __('Finish your current load to create a new one.')
            ], 403);
        }

        $data = $request->all();
        $loadStatus = LoadStatusEnum::ACCEPTED;

        // Required data to create a new Load...

        $data['date'] = Carbon::now();
        $data['driver_id'] = $driver->id;
        $data['status'] = $loadStatus;
        $data['load_type_id'] = 1; //need to change this to null in database;
        $data['control_number'] = $request->control_number ?? "undefined";
        //$data['origin'] = null;
        $data['customer_po'] = ""; // Should be nullable in db
        $data['customer_reference'] = ""; // Should be nullable in db

        $trip = Trip::find($request->trip_id);
        // Trip related info
        $data['id'] = $trip->id;
        $data['origin'] = $trip->trip_origin ? $trip->trip_origin->name : $trip->origin;
        $data['origin_coords'] = $trip->trip_origin ? $trip->trip_origin->coords : $trip->origin_coords;
        $data['destination'] = $trip->trip_destination ? $trip->trip_destination->name : $trip->destination;
        $data['destination_coords'] = $trip->trip_destination ? $trip->trip_destination->coords : $trip->destination_coords;
        $data['customer_name'] = $trip->customer_name;
        $data['mileage'] = $trip->mileage;
        $data['shipper_id'] = $trip->shipper_id;
        $data['broker_id'] = $driver->broker_id ?? Driver::find($driver->id)->broker_id;

        $load = $this->storeUpdate($data);
        $this->switchLoadStatus($load, $loadStatus);

        $driver->status = 'active';
        $driver->save();

        $botAnswers = BotAnswers::where('driver_id', $driver->id)->delete();

        return response([
            'status' => 'ok',
            'message' => 'The load has been successfully created!',
            'load' => $load
        ]);
    }

    public function getTrips(Request $request)
    {
        $shippers = [];

        foreach (auth()->user()->shippers as $shipper){
            $shippers[] = $shipper->id;
        }

        $query = Trip::select([
            'id as key',
            //DB::raw("CONCAT(name, ': ', origin, ' - ', destination) as value"),
            "name as value",
        ])
            ->whereHas('broker', function ($q) {
                $q->where('id', auth()->user()->broker_id);
            })
            ->whereIn('shipper_id', $shippers)
            ->where("name", "LIKE", "%$request->search%");

        return response([
            'status' => 'ok',
            'message' => 'Found trips',
            'trips' => KeyValueResource::collection($query->get()),
        ]);
    }

    public function getActive(Request $request)
    {
        $driver = auth()->user();

        $activeLoad = DB::table('loads')
            ->where('driver_id', $driver->id)
            ->whereNull('deleted_at')
            ->whereNotIn('status', [LoadStatusEnum::UNALLOCATED, LoadStatusEnum::FINISHED])
            ->first();

        if (empty($activeLoad)) {
            $message = __('Not active load');
            $load = $activeLoad;
        } else {
            /**
             * Temporary fix, the previous query does return the load but in a stdObject instance,
             * we need to call the eloquent constructor to get a model instance for further methods
             */
            $activeLoad = Load::find($activeLoad->id);

            $message = __('Active load found');
            $load = new LoadResource($activeLoad);
        }

        return response([
            'status' => 'ok',
            'message' => $message,
            'load' => $load
        ]);
    }

    public function getPendingToRespond()
    {
        $driver = auth()->user();

        $pendingLoad = $driver->loads
            ->where('status', LoadStatusEnum::REQUESTED)
            ->first();

        if (empty($pendingLoad)) {
            $message = __('No pending loads available');
            $load = $pendingLoad;
        } else {
            $message = __('You have one pending load to respond');
            $load = new LoadResource($pendingLoad);
        }

        return response([
            'status' => 'ok',
            'message' => $message,
            'load' => $load
        ]);
    }

    /**
     * @throws ShiftNotActiveException
     */
    public function accept(Request $request)
    {
        $driver = auth()->user();

        $load = Load::findOrFail($request->get('load_id'));
        if ($load->status === LoadStatusEnum::REQUESTED || $load->status === LoadStatusEnum::UNALLOCATED) {

            $loadStatus = $this->switchLoadStatus($load, LoadStatusEnum::ACCEPTED);

            $shift = $driver->shift;

            if (empty($shift))
                throw new ShiftNotActiveException();

            // Assign the box details to load coming from shift
            $load->box_status_init = $shift->box_status;
            $load->box_type_id_init = $shift->box_type_id;
            $load->box_number_init = $shift->box_number;
            $load->update();

            return response([
                'status' => 'ok',
                'load_status' => LoadStatusEnum::ACCEPTED,
                'load_status_details' => new LoadStatusResource($loadStatus)
            ]);
        }

        return response([
            'status' => 'error', 'message' => 'The current load status is not valid to proceed.'
        ], 400);
    }

    public function reject(Request $request)
    {
       /* $driver = auth()->user();
        $loadId = $request->get('load_id');

        // Register load rejection
        /* RejectedLoad::create([ // remove for test only
             'load_id' => $loadId,
             'driver_id' => $driver->id,
         ]);*/
/*
        // Remove the driver from this load
        $load = Load::where('status', LoadStatusEnum::REQUESTED)->find($loadId);
        if (!$load) {
            return response([
                'status' => 'error',
                'message' => __("You're unable to rejected this load at the current status")
            ], 400);
        }
        $load->driver_id = null;
        $load->update();

        $this->switchLoadStatus($load, LoadStatusEnum::UNALLOCATED);

        $maxLoadRejections = AppConfig::where('key', AppConfigEnum::MAX_LOAD_REJECTIONS)->first();

        if (empty($maxLoadRejections)) {
            abort(500, 'The value MAX_LOAD_REJECTIONS has not been created in database configs');
        }

        if ($driver->rejections->count() == $maxLoadRejections->value) {

            /**
             * End shift could throw a DriverHasUnfinishedLoadsException, but at this point of the process is illogic that scenario,
             * just ignore that exception
             **/

      /*      $this->endShift($driver);

            return response([
                'status' => 'ok',
                'message' => 'You have been reached the maximum rejection times, your shift has been ended automatically.',
                'reached_max_rejections' => true,
                'load_status' => LoadStatusEnum::UNALLOCATED
            ]);
        }

        // As the user has been pushed out of the AvailableDrivers table when load has been assigned,
        // we must add him to queue again. At this point, the driver can keep its shift as have not reached
        // the max rejections amount.

        $this->registryInAvailableDriversQueue($driver);

        $message = $request->get('is_automatic') ?
            'The load has been rejected automatically due no response' :
            'The load has been rejected successfully';

        return response([
            'status' => 'ok',
            'message' => $message,
            'reached_max_rejections' => false,
            'load_status' => LoadStatusEnum::UNALLOCATED,
            'load_status_details' => new LoadStatusResource($load->loadStatus)
        ]);*/

    }

    public function loading(Request $request)
    {

        $loadId = $request->get('load_id');
        $load = Load::findOrFail($loadId);

        if ($load->status === LoadStatusEnum::ACCEPTED) {
            $loadStatus = $this->switchLoadStatus($load, LoadStatusEnum::LOADING);

            $load->customer_po = $request->get('customer_po');
            $load->update();

            return response([
                'status' => 'ok',
                'load_status' => LoadStatusEnum::LOADING,
                'load_status_details' => new LoadStatusResource($loadStatus)
            ]);
        }

        return response([
            'status' => 'error', 'message' => 'The current load status is not valid to proceed.'
        ], 400);
    }

    public function toLocation(Request $request)
    {
        $receipt = $request->get('receipt');
        $loadId = $request->get('load_id');
        if (empty($receipt)) {
            return response('You must attach a valid voucher', 400);
        }

        $load = Load::findOrFail($loadId);
        if ($load->status === LoadStatusEnum::LOADING) {
            $load->customer_reference = $request->get('sand_ticket');
            $load->weight = $request->get('weight');
            $load->silo_number = $request->get('silo_number');
            $load->tons = (float)$request->get('weight') / 2000;
            $load->update();

            $loadStatus = $this->switchLoadStatus($load, LoadStatusEnum::TO_LOCATION);

            $voucher = $this->uploadImage(
                $receipt,
                'loads/' . $loadStatus->id,
                50,
                'jpg',
            );

            $loadStatus->to_location_voucher = $voucher;
            $loadStatus->update();

            return response([
                'status' => 'ok',
                'load_status' => LoadStatusEnum::TO_LOCATION,
                'load_status_details' => new LoadStatusResource($loadStatus)
            ]);
        }

        return response([
            'status' => 'error', 'message' => 'The current load status is not valid to proceed.'
        ], 400);
    }

    public function arrived(Request $request)
    {
        $loadId = $request->get('load_id');
        $load = Load::findOrFail($loadId);
        if ($load->statur === LoadStatusEnum::TO_LOCATION) {
            $loadStatus = $this->switchLoadStatus($load, LoadStatusEnum::ARRIVED);
            $loadStatus->update();

            return response([
                'status' => 'ok',
                'load_status' => LoadStatusEnum::ARRIVED,
                'load_status_details' => new LoadStatusResource($loadStatus)
            ]);
        }

        return response([
            'status' => 'error', 'message' => 'The current load status is not valid to proceed.'
        ], 400);
    }

    public function unloading(Request $request)
    {
        $loadId = $request->get('load_id');
        $load = Load::findOrFail($loadId);
        if ($load->statur === LoadStatusEnum::ARRIVED) {
            $loadStatus = $this->switchLoadStatus($load, LoadStatusEnum::UNLOADING);

            return response([
                'status' => 'ok',
                'load_status' => LoadStatusEnum::UNLOADING,
                'load_status_details' => new LoadStatusResource($loadStatus)
            ]);
        }

        return response([
            'status' => 'error', 'message' => 'The current load status is not valid to proceed.'
        ], 400);
    }

    public function finished(Request $request)
    {
        $loadId = $request->get('load_id');
        $driver = auth()->user();

        $receipt = $request->get('receipt');
        if (empty($receipt)) {
            return response('You must attach a valid voucher', 400);
        }

        $load = Load::findOrFail($loadId);
        if ($load->status === LoadStatusEnum::UNLOADING) {

            $date = Carbon::now();
            $load->bol = $request->get('bol');

            $dispatch = DispatchSchedule::where('day', $date->dayOfWeek-1)
                ->where('time', $date->format("H").':00:00')->first();
            if ($dispatch)
                $load->dispatch_id = $dispatch->user_id;

            $load->update();

            $loadStatus = $this->switchLoadStatus($load, LoadStatusEnum::FINISHED);

            $voucher = $this->uploadImage(
                $receipt,
                'loads/' . $loadStatus->id,
                50,
                'jpg',
            );
            $loadStatus->finished_voucher = $voucher;
            $loadStatus->update();

            $this->endShift($driver);

            BotLoadReminder::dispatch([$driver->id])->delay(now()->addMinutes(AppConfig::where('key', AppConfigEnum::TIME_AFTER_LOAD_REMINDER)->first()->value/60));

            $canActivate = 1; //Temporal not checking shift
            //if (Broker::find(1)->active_shifts){
            //    $canActivate = $driver->canActiveShift();
            //} else {
            //    $canActivate = 1;
            //}
            // Check if driver can accept more loads and attach to response
            return response([
                'status' => 'ok',
                'can_keep_shift' => true,
                'load_status' => LoadStatusEnum::FINISHED,
                'load_status_details' => new LoadStatusResource($loadStatus)
            ]);
        }

        return response([
            'status' => 'error', 'message' => 'The current load status is not valid to proceed.'
        ], 400);
    }

    public function updateEndBox(Request $request)
    {
        $load = Load::find($request->get('load_id'));

        // Assign the box details to load coming from shift
        $load->box_status_end = $request->get('box_status');
        $load->box_type_id_end = $request->get('box_type_id');
        $load->box_number_end = $request->get('box_number');
        $load->update();

        return response([
            'status' => 'ok',
            'message' => 'Your box have been saved successfully'
        ]);
    }

}
