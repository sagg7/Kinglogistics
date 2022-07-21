<?php

namespace App\Http\Controllers;

use App\Http\Middleware\Timezone;
use App\Traits\EloquentQueryBuilder\GetSelectionData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TimezoneController extends Controller
{
    use GetSelectionData;

    /**
     * @param Request $request
     * @return array
     */
    public function selection(Request $request): array
    {
        $query = Timezone::select([
            'id',
            DB::raw('CONCAT("(", abbreviation, ") ", name) AS text')
        ])
            ->where("name", "LIKE", "%$request->search%")
            ->orWhere("abbreviation", "LIKE", "%$request->search%");

        return $this->selectionData($query, $request->take, $request->page);
    }
}
