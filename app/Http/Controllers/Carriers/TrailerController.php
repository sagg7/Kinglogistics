<?php

namespace App\Http\Controllers\Carriers;

use App\Http\Controllers\Controller;
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
        $query = Trailer::select([
            'id',
            'number as text',
        ])
            ->where("number", "LIKE", "%$request->search%")
            ->whereDoesntHave("truck")
            ->whereNull("inactive");

        return $this->selectionData($query, $request->take, $request->page);
    }
}
