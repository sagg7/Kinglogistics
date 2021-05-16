<?php

namespace App\Traits\EloquentQueryBuilder;

trait GetSimpleSearchData
{
    private function simpleSearchData($query, $request)
    {
        // Skip-Take data
        $take = $request->endRow;
        $current = $request->startRow / $take;
        $skip = $take * $current;

        if ($request->filterModel)
            foreach ($request->filterModel as $key => $item) {
                $filterArr = $this->generateFilters($item['filterType'], $item['type'], $item['filter']);
                $statement = $filterArr['statement'];

                $query->where(function ($q) use ($filterArr, $statement, $key) {
                    $q->$statement($key, $filterArr['comparative'], $filterArr['string']);
                });
            }

        if ($request->searchable)
            $query->where(function ($q) use ($request) {
                foreach ($request->searchable as $i => $item) {
                    ($i == 0) ? $statement = "where" : $statement = "orWhere";
                    $q->$statement($item, 'LIKE', "%$request->search%");
                }
            });

        if ($request->sortModel) {
            $column = $request->sortModel[0]['colId'];
            $dir = $request->sortModel[0]['sort'];
            $query->orderBy($column, $dir);
        }

        $total = $query->count();
        $query = $query->skip($skip)->take($take);
        $result = $query->get();

        $params = [
            'rows' => $result,
            'lastRow' => $total,
        ];

        return response()->json($params);
    }
}
