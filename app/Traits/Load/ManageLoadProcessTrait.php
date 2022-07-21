<?php

namespace App\Traits\Load;

use App\Events\LoadUpdate;
use App\Models\Load;
use App\Models\LoadStatus;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

trait ManageLoadProcessTrait {

    // Move this method to a Trait: Useful for Load creation scenarios...
    private function switchLoadStatus($load, string $status, Carbon $timestamp = null): LoadStatus
    {
        //$load = Load::find($loadId);

        if (empty($load)) {
            abort(404, 'The requested load has not been found');
        }

        return DB::transaction(function () use ($load, $status, $timestamp) {
            $load->status = $status;
            $load->save();

            $loadStatus = $load->load('loadStatus')->loadStatus;

            // If this load does not have a load status entry, create one and assign the incoming status
            if (empty($loadStatus)) {
                $loadStatus = LoadStatus::create([
                    'load_id' => $load->id
                ]);
            }

            // Update load statuses table
            $loadStatus[$status . '_timestamp'] = $timestamp ?: Carbon::now();
            $loadStatus->update();

            event(new LoadUpdate($load));

            return $loadStatus;
        });
    }

}
