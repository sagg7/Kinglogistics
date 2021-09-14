<?php

namespace App\Traits\EloquentQueryBuilder;

trait GetSimpleSearchData
{
    private function simpleSearchData($query, $request, $mainStatement = 'where')
    {
        // Skip-Take data
        $take = $request->endRow;
        $current = $request->startRow / $take;
        $skip = $take * $current;

        if ($request->filterModel)
            foreach ($request->filterModel as $key => $item) {
                $filterArr = $this->generateFilters($item['filterType'], $item['type'], $item['filter']);
                $statement = $filterArr['statement'];

                $query->$mainStatement(function ($q) use ($filterArr, $statement, $key) {
                    $q->$statement($key, $filterArr['comparative'], $filterArr['string']);
                });
            }

        if ($request->searchable)
            $query->$mainStatement(function ($q) use ($request) {
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

    private function multiTabSearchData($query, $request, $relationships = [])
    {
        // Skip-Take data
        $take = $request->endRow;
        $current = $request->startRow / $take;
        $skip = $take * $current;

        $query->where(function ($q) use ($request, $relationships) {
            if ($request->filterModel)
                foreach ($request->filterModel as $key => $item) {
                    $filterArr = $this->generateFilters($item['filterType'], $item['type'], $item['filter']);
                    $statement = $filterArr['statement'];

                    $q->where(function ($q) use ($filterArr, $statement, $key) {
                        $q->$statement($key, $filterArr['comparative'], $filterArr['string']);
                    });
                }

            if ($request->searchable)
                $q->where(function ($q) use ($request, $relationships) {
                    $first = true;
                    foreach ($request->searchable as $i => $item) {
                        $statement = $first ? "where" : "orWhere";
                        $q->$statement($item, 'LIKE', "%$request->search%");
                        if ($first)
                            $first = false;
                    }
                    foreach ($relationships as $item) {
                        $statement = $first ? "whereHas" : "orWhereHas";
                        $q->$statement($item['relation'], function ($q) use ($request, $item) {
                            $q->where($item['column'], 'LIKE', "%$request->search%");
                        });
                        if ($first)
                            $first = false;
                    }
                });
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
