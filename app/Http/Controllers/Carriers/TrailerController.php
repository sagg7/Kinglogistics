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
            ->whereNull("inactive")
            ->whereHas('rentals', function ($q) {
                $q->where('carrier_id', auth()->user()->id);
            });

        return $this->selectionData($query, $request->take, $request->page);
    }
}
