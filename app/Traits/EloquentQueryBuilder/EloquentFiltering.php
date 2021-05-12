<?php

namespace App\Traits\EloquentQueryBuilder;

trait EloquentFiltering {
    private function searchNameLastName($query, $string, $column_name = 'name', $column_last_name = 'last_name') {
        $name_arr = explode(" ", trim(($string)));
        $count = count($name_arr);
        $name = '';
        $last_name = '';
        $alt_name = '';
        $alt_last_name = '';
        if ($count == 3) {
            $name = $name_arr[0];
            $last_name = $name_arr[1] . ' ' . $name_arr[2];

            $alt_name = $name_arr[0] . ' ' . $name_arr[1];
            $alt_last_name = $name_arr[2];
        } else if ($count > 3) {
            foreach ($name_arr as $i => $item) {
                if ($i < 2) {
                    $name .= ' ' . $item;
                } else {
                    $last_name .= ' ' . $item;
                }
            }
        } else {
            foreach ($name_arr as $i => $item) {
                if ($i == 0)
                    $name = $item;
                else
                    $last_name .= ' ' . $item;
            }
        };
        $name = trim($name);

        $last_name = trim($last_name);
        $query->orWhere(function ($q) use ($name, $last_name, $column_name, $column_last_name) {
            $q->where($column_name, "LIKE", "%$name%")
                ->where($column_last_name, "LIKE", "%$last_name%");
        });

        if ($alt_name)
            $query->orWhere(function ($q) use ($alt_name, $alt_last_name, $column_name, $column_last_name) {
                $q->where($column_name, "LIKE", "%$alt_name%")
                    ->where($column_last_name, "LIKE", "%$alt_last_name%");
            });

        $trimmed = trim($string);
        $query->orWhere('last_name', "LIKE", "%$trimmed%");
    }
}
