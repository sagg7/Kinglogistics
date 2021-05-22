<?php

namespace App\Http\Controllers;

use App\Models\Truck;
use App\Traits\EloquentQueryBuilder\GetSelectionData;
use Illuminate\Http\Request;

class TruckController extends Controller
{
    use GetSelectionData;

    /**
     * @param Request $request
     * @return array
     */
    public function selection(Request $request): array
    {
        $query = Truck::select([
            'id',
            'number as text',
        ])
            ->where("number", "LIKE", "%$request->search%")
            /*->whereHas("driver", function ($q) use ($request) {
                if ($request->driver)
                    $q->where("id", $request->driver);
            })*/
            ->where(function ($q) use ($request) {
                if ($request->carrier)
                    $q->where("carrier_id", $request->carrier);
            })
            ->whereNull("inactive");

        if ($request->type === "drivers") {
            $query->whereDoesntHave('driver');
        }

        return $this->selectionData($query, $request->take, $request->page);
    }
}
