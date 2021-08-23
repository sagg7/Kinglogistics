<?php

namespace App\Http\Controllers\Api;

use App\Enums\AppConfigEnum;
use App\Enums\LoadStatusEnum;
use App\Exceptions\ShiftNotActiveException;
use App\Http\Controllers\Controller;
use App\Http\Resources\Drivers\LoadResource;
use App\Models\AppConfig;
use App\Models\Load;
use App\Models\LoadStatus;
use App\Models\RejectedLoad;
use App\Traits\Shift\ShiftTrait;
use App\Traits\Storage\FileUpload;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LoadController extends Controller
{

    use ShiftTrait, FileUpload;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getRecords()
    {
        $driver = auth()->user();

        $availableLoads = $driver->loads->where('status', LoadStatusEnum::FINISHED);

        $loads = LoadResource::collection($availableLoads);

        return response($loads, 200);
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

        RejectedLoad::create([
            'load_id' => $loadId,
            'driver_id' => $driver->id,
        ]);

        $this->switchLoadStatus($loadId, LoadStatusEnum::UNALLOCATED);

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
        $receipt = $request->get('receipt');
        if (empty($receipt)) {
            return response('You must attach a valid voucher', 400);
        }

        $loadStatus = $this->switchLoadStatus($request->get('load_id'), LoadStatusEnum::TO_LOCATION);

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

        return response(['status' => 'ok', 'load_status' => LoadStatusEnum::TO_LOCATION]);
    }

    public function arrived(Request $request)
    {
        $load = $this->switchLoadStatus($request->get('load_id'), LoadStatusEnum::ARRIVED);

        // Do required stuff for "Arrived" event

        return response(['status' => 'ok', 'load_status' => LoadStatusEnum::ARRIVED]);
    }

    public function unloading(Request $request)
    {
        $load = $this->switchLoadStatus($request->get('load_id'), LoadStatusEnum::UNLOADING);

        // Do required stuff for "Unloading" event

        return response(['status' => 'ok', 'load_status' => LoadStatusEnum::UNLOADING]);
    }

    public function finished(Request $request)
    {
        $driver = auth()->user();

        $receipt = $request->get('receipt');
        if (empty($receipt)) {
            return response('You must attach a valid voucher', 400);
        }

        $loadStatus = $this->switchLoadStatus($request->get('load_id'), LoadStatusEnum::FINISHED);

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

        // Check if driver can accept more loads and attach to response
        return response([
            'status' => 'ok',
            'can_keep_shift' => $driver->canActiveShift(),
            'load_status' => LoadStatusEnum::FINISHED
        ]);
    }

    public function updateEndBox(Request $request) {
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

    // Move this method to a Trait: Useful for Load creation scenarios...
    private function switchLoadStatus($loadId, string $status): LoadStatus
    {
        $load = Load::find($loadId);

        if (empty($load)) {
            abort(404, 'The requested load has not been found');
        }

        $load->status = $status;
        $load->save();

        $loadStatus = $load->loadStatus;

        // If this load does not have a load status entry, create one and assign the incoming status
        if (empty($loadStatus)) {
            $loadStatus = LoadStatus::create([
                'load_id' => $load->id
            ]);
        }

        // Update load statuses table
        $loadStatus[$status . '_timestamp'] = Carbon::now();
        $loadStatus->update();

        return $loadStatus;
    }

}
