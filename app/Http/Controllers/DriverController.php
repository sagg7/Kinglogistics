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
        if (!$request->carrier)
            abort(404);

        $query = Driver::select([
            'id',
            'name as text',
            'drivers.trailer_id'
        ])
            ->where("name", "LIKE", "%$request->name%")
            ->where("carrier_id", $request->carrier)
            ->with('trailer:id,number');

        return $this->selectionData($query, $request->take, $request->page);
    }
}
