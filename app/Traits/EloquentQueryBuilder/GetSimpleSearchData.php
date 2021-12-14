<?php

namespace App\Traits\EloquentQueryBuilder;

use Carbon\Carbon;

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

    private function multiTabSearchData($query, $request, $relationsFunc = null, $mainStatement = 'where')
    {
        // Skip-Take data
        $take = $request->endRow;
        $current = $request->startRow / $take;
        $skip = $take * $current;

        $relationships = [];
        if ($relationsFunc) {
            if ($request->searchable) {
                $searchable = [];
                foreach ($request->searchable as $item) {
                    $array = $this->$relationsFunc($item);
                    if ($array) {
                        $relationships[] = $array;
                    } else {
                        $searchable[count($searchable) + 1] = $item;
                    }
                }
                $request->searchable = $searchable;
            }

            if ($request->sortModel) {
                foreach ($request->sortModel as $item) {
                    $array = $this->$relationsFunc($item['colId']);
                    if ($array) {
                        $relationships[] = $array;
                    }
                }
            }
        }

        $query->$mainStatement(function ($q) use ($request, $relationships) {
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

        $sortAfterQuery = null;
        if ($request->sortModel) {
            $key = array_search($request->sortModel[0]['colId'], array_column($relationships, 'relation'));
            $key = $key !== false ? $key : array_search($request->sortModel[0]['colId'], array_column($relationships, 'result_relation'));
            if ($key === false) {
                $column = $request->sortModel[0]['colId'];
                $dir = $request->sortModel[0]['sort'];
                $query->orderBy($column, $dir);
            } else {
                $item = $relationships[$key];
                $dir = $request->sortModel[0]['sort'] === 'asc' ? SORT_ASC : SORT_DESC;
                $sortAfterQuery = ['relation' => $item['result_relation'] ?? $item['relation'], 'column' =>  $item['column'], 'direction' => $dir];
            }
        }

        $total = $query->count();
        $query = $query->skip($skip)->take($take);
        $result = $query->get();

        if ($sortAfterQuery) {
            $result = $result->toArray();
            $sorting = [];
            foreach ($result as $key => $row)
            {
                $sorting[$key][$sortAfterQuery['relation']] = $row[$sortAfterQuery['relation']][$sortAfterQuery['column']] ?? null;
            }
            array_multisort($sorting, $sortAfterQuery['direction'], $result);
        }

        return [
            'now' => Carbon::now('America/Chicago'),
            'rows' => $result,
            'lastRow' => $total,
        ];
    }
}
