<?php

namespace App\Http\Controllers\Api;

use App\Enums\AppConfigEnum;
use App\Enums\LoadStatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Resources\Drivers\LoadResource;
use App\Models\AppConfig;
use App\Models\Load;
use App\Models\LoadStatus;
use App\Models\RejectedLoad;
use App\Traits\Shift\ShiftTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;

class LoadController extends Controller
{

    use ShiftTrait;

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

        $pendingLoad = $driver->loads->where('status', LoadStatusEnum::UNALLOCATED)->first();

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

    public function accept(Request $request)
    {
        $load = $this->switchLoadStatus($request->get('load_id'), LoadStatusEnum::ACCEPTED);

        // Do stuff required for "On accept" event

        return response(['status' => 'ok']);
    }

    public function reject(Request $request)
    {
        $driver = auth()->user();

        RejectedLoad::create([
            'load_id' => $request->get('load_id'),
            'driver_id' => $driver->id,
        ]);

        $maxLoadRejections = AppConfig::where('key', AppConfigEnum::MAX_LOAD_REJECTIONS)->first();

        if (empty($maxLoadRejections)) {
            abort(500, 'The value MAX_LOAD_REJECTIONS has not been created in database configs');
        }

        if ($driver->rejections->count() == $maxLoadRejections->value) {

            // End the driver
            $this->endShift($driver);

            return response([
                'status' => 'ok',
                'message' => 'You have been reached the maximum rejection times, your shift has been ended automatically.',
                'reached_max_rejections' => true,
            ]);
        }

        return response([
            'status' => 'ok',
            'message' => 'The load has been rejected successfully',
            'reached_max_rejections' => false
        ]);

    }

    public function loading(Request $request)
    {
        $load = $this->switchLoadStatus($request->get('load_id'), LoadStatusEnum::LOADING);

        // Do required stuff for "Loading" event

        return response(['status' => 'ok']);
    }

    public function toLocation(Request $request)
    {
        $load = $this->switchLoadStatus($request->get('load_id'), LoadStatusEnum::TO_LOCATION);

        // Do required stuff for "Loading" event

        return response(['status' => 'ok']);
    }

    public function arrived(Request $request)
    {
        $load = $this->switchLoadStatus($request->get('load_id'), LoadStatusEnum::ARRIVED);

        // Do required stuff for "Arrived" event

        return response(['status' => 'ok']);
    }

    public function unloading(Request $request)
    {
        $load = $this->switchLoadStatus($request->get('load_id'), LoadStatusEnum::UNLOADING);

        // Do required stuff for "Unloading" event

        return response(['status' => 'ok']);
    }

    public function finished(Request $request)
    {
        $load = $this->switchLoadStatus($request->get('load_id'), LoadStatusEnum::FINISHED);

        // Do required stuff for "Finished" event

        return response(['status' => 'ok']);
    }

    // Move this method to a Trait: Useful for Load creation scenarios...
    private function switchLoadStatus($loadId, string $status): Load
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

        return $load;
    }

}
