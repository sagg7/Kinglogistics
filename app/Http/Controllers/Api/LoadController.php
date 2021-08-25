<?php

namespace App\Http\Controllers\Api;

use App\Enums\AppConfigEnum;
use App\Enums\LoadStatusEnum;
use App\Exceptions\ShiftNotActiveException;
use App\Http\Controllers\Controller;
use App\Http\Resources\Drivers\LoadResource;
use App\Models\AppConfig;
use App\Models\AvailableDriver;
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
            ->sortByDesc('id');

        $loads = LoadResource::collection($availableLoads);

        return response($loads, 200);
    }

    public function storeLoad(Request $request)
    {
        $driver = auth()->user();
        $data = $request->all();
        $loadStatus = LoadStatusEnum::ACCEPTED;

        // Required data to create a new Load...

        $data['date'] = Carbon::now();
        $data['driver_id'] = $driver->id;
        $data['status'] = $loadStatus;
        $data['load_type_id'] = 1; //need to change this to null in database;
        $data['control_number'] = $request->control_number ?? "undefined";
        $data['origin'] = null;
        $data['customer_po'] = ""; // Should be nullable in db
        $data['customer_reference'] = ""; // Should be nullable in db

        $trip = Trip::find($request->trip_id);
        // Trip related info
        $data['id'] = $trip->id;
        $data['origin'] = $trip->origin;
        $data['origin_coords'] = $trip->origin_coords;
        $data['destination'] = $trip->destination;
        $data['destination_coords'] = $trip->destination_coords;
        $data['customer_name'] = $trip->customer_name;
        $data['mileage'] = $trip->mileage;

        $load = $this->storeUpdate($data);
        $this->switchLoadStatus($load->id, $loadStatus);

        return response($load, 200);
    }

    public function getTrips(Request $request)
    {
        $query = Trip::select([
            'id',
            DB::raw("CONCAT(name, ': ', origin, ' - ', destination) as text"),
        ])
            ->where("name", "LIKE", "%$request->search%");

        return response($query->get(), 200);
    }

    public function getActive(Request $request)
    {
        $driver = auth()->user();

        $activeLoad = DB::table('loads')
            ->where('driver_id', $driver->id)
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
        $this->switchLoadStatus($request->get('load_id'), LoadStatusEnum::ACCEPTED);

        $load = Load::find($request->get('load_id'));
        $shift = $driver->shift;

        if (empty($shift))
            throw new ShiftNotActiveException();

        // Assign the box details to load coming from shift
        $load->box_status_init = $shift->box_status;
        $load->box_type_id_init = $shift->box_type_id;
        $load->box_number_init = $shift->box_number;
        $load->update();

        return response(['status' => 'ok', 'load_status' => LoadStatusEnum::ACCEPTED]);
    }

    public function reject(Request $request)
    {
        $driver = auth()->user();
        $loadId = $request->get('load_id');

        // Register load rejection
        RejectedLoad::create([
            'load_id' => $loadId,
            'driver_id' => $driver->id,
        ]);

        $this->switchLoadStatus($loadId, LoadStatusEnum::UNALLOCATED);

        // Remove the driver from this load
        $load = Load::find($loadId);
        $load->driver_id = null;
        $load->update();

        $maxLoadRejections = AppConfig::where('key', AppConfigEnum::MAX_LOAD_REJECTIONS)->first();

        if (empty($maxLoadRejections)) {
            abort(500, 'The value MAX_LOAD_REJECTIONS has not been created in database configs');
        }

        if ($driver->rejections->count() == $maxLoadRejections->value) {

            /**
             * End shift could throw a DriverHasUnfinishedLoadsException, but at this point of the process is illogic that scenario,
             * just ignore that exception
             **/

            $this->endShift($driver);

            return response([
                'status' => 'ok',
                'message' => 'You have been reached the maximum rejection times, your shift has been ended automatically.',
                'reached_max_rejections' => true,
                'load_status' => LoadStatusEnum::UNALLOCATED
            ]);
        }

        $message = $request->get('is_automatic') ?
            'The load has been rejected automatically due no response' :
            'The load has been rejected successfully';

        return response([
            'status' => 'ok',
            'message' => $message,
            'reached_max_rejections' => false,
            'load_status' => LoadStatusEnum::UNALLOCATED
        ]);

    }

    public function loading(Request $request)
    {
        $load = $this->switchLoadStatus($request->get('load_id'), LoadStatusEnum::LOADING);

        // Do required stuff for "Loading" event

        return response(['status' => 'ok', 'load_status' => LoadStatusEnum::LOADING]);
    }

    public function toLocation(Request $request)
    {
        $loadStatus = $this->switchLoadStatus($request->get('load_id'), LoadStatusEnum::TO_LOCATION);

        // Do required stuff for "ToLocation" event

        return response(['status' => 'ok', 'load_status' => LoadStatusEnum::TO_LOCATION]);
    }

    public function arrived(Request $request)
    {
        $receipt = $request->get('receipt');
        $loadId = $request->get('load_id');
        if (empty($receipt)) {
            return response('You must attach a valid voucher', 400);
        }

        $load = Load::find($loadId);
        $load->sand_ticket = $request->get('sand_ticket');
        $load->weight = $request->get('weight');
        $load->update();

        $loadStatus = $this->switchLoadStatus($loadId, LoadStatusEnum::ARRIVED);

        $voucher = $this->uploadImage(
            $receipt,
            'loads/' . $loadStatus->id,
            50,
            null,
            false,
            true,
            'jpg');

        $loadStatus->to_location_voucher = $voucher;
        $loadStatus->update();


        return response(['status' => 'ok', 'load_status' => LoadStatusEnum::ARRIVED]);
    }

    public function unloading(Request $request)
    {
        $loadId = $request->get('load_id');
        $load = Load::find($loadId);

        $this->switchLoadStatus($loadId, LoadStatusEnum::UNLOADING);

        $load->customer_po = $request->get('customer_po');
        $load->update();

        return response(['status' => 'ok', 'load_status' => LoadStatusEnum::UNLOADING]);
    }

    public function finished(Request $request)
    {
        $loadId = $request->get('load_id');
        $driver = auth()->user();

        $receipt = $request->get('receipt');
        if (empty($receipt)) {
            return response('You must attach a valid voucher', 400);
        }

        $load = Load::find($loadId);
        $load->bol = $request->get('bol');
        $load->update();

        $loadStatus = $this->switchLoadStatus($loadId, LoadStatusEnum::FINISHED);

        $voucher = $this->uploadImage(
            $receipt,
            'loads/' . $loadStatus->id,
            50,
            null,
            false,
            true,
            'jpg');
        $loadStatus->finished_voucher = $voucher;
        $loadStatus->update();

        $this->endShift($driver);

        // Check if driver can accept more loads and attach to response
        return response([
            'status' => 'ok',
            'can_keep_shift' => $driver->canActiveShift(),
            'load_status' => LoadStatusEnum::FINISHED
        ]);
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
