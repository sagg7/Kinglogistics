<?php

namespace App\Traits\EloquentQueryBuilder;

trait GetSelectionData
{
    private function selectionData($query, $take, $page)
    {
        $count = $query->count();
        $current = $page - 1;
        $skip = $take * $current;
        $query = $query->skip($skip)->take($take)->get();

        $more = ($page * $take) < $count;

        return [
            "results" => $query,
            "pagination" => [
                "more" => $more
            ],
        ];
    }
}
