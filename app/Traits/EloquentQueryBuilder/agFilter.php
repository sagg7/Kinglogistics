<?php

namespace App\Traits\EloquentQueryBuilder;

trait agFilter {
    /**
     * @param string $filterType
     * @param string $type
     * @param string $filter
     * @return array
     */
    private function generateFilters(string $filterType, string $type, string $filter)
    {
        switch ($filterType) {
            default:
            case 'text':
                $statement = 'where';
                break;
            case 'date':
                $statement = 'whereDate';
                break;
        }

        switch ($type) {
            default:
            case 'contains':
                $comparative = "LIKE";
                $string = "%$filter%";
                break;
            case 'notContains':
                $comparative = "NOT LIKE";
                $string = "%$filter%";
                break;
            case 'equals':
                $comparative = "=";
                $string = $filter;
                break;
            case 'notEqual':
                $comparative = "!=";
                $string = $filter;
                break;
            case 'startsWith':
                $comparative = "LIKE";
                $string = "$filter%";
                break;
            case 'endsWith':
                $comparative = "LIKE";
                $string = "%$filter";
                break;
        }

        return compact( 'statement', 'comparative', 'string');
    }
}
