<?php

namespace App\Http\Controllers;

use App\Models\Trailer;
use App\Traits\EloquentQueryBuilder\GetSelectionData;
use Illuminate\Http\Request;

class TrailerController extends Controller
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

        $query = Trailer::select([
            'id',
            'number as text',
        ])
            ->where("number", "LIKE", "%$request->name%")
            ->where("carrier_id", $request->carrier);

        return $this->selectionData($query, $request->take, $request->page);
    }
}
