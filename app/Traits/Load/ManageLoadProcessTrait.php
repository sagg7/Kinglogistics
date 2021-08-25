<?php

namespace App\Traits\Load;

use App\Models\Load;
use App\Models\LoadStatus;
use Carbon\Carbon;

trait ManageLoadProcessTrait {

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
