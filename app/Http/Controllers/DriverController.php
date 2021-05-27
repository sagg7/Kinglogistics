<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use App\Traits\EloquentQueryBuilder\GetSelectionData;
use Illuminate\Http\Request;

class DriverController extends Controller
{
    use GetSelectionData;

    /**
     * @param Request $request
     * @return array
     */
    public function selection(Request $request): array
    {
        $query = Driver::select([
            'id',
            'name as text',
        ])
            ->where("name", "LIKE", "%$request->search%")
            ->where(function ($q) use ($request) {
                if ($request->carrier)
                    $q->where("carrier_id", $request->carrier);
            })
            ->whereHas("carrier", function ($q) {
                $q->whereNull("inactive");
            })
            ->where(function ($q) use ($request) {
                if ($request->rental)
                    $q->whereDoesntHave("truck")
                        ->whereDoesntHave("trailer");
            })
            ->with('truck.trailer:id,number');

        return $this->selectionData($query, $request->take, $request->page);
    }
}
