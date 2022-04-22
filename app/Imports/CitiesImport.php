<?php

namespace App\Imports;

use App\Models\City;
use App\Models\CityZipLocation;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\WithChunkReading;

// If you need to run it again, dispatch it as a queue, see the laravel queue documentation for further reference:
// @link https://docs.laravel-excel.com/3.1/imports/queued.html     =>  How to dispatch the Excel queue
// @link https://laravel.com/docs/9.x/queues                        =>  How to init the queue worker

class CitiesImport implements ToArray, WithChunkReading, ShouldQueue
{
    public function chunkSize(): int
    {
        return 35000;
    }

    /**
    * @param Collection $collection
    */
    public function array(array $array)
    {
        $data = [];
        //$now = Carbon::now();
        foreach ($array as $row) {
            $locations = new CityZipLocation([
                "latitude" => $row[1],
                "longitude" => $row[2],
                "zipcode" => $row[0],
            ]);
            $city_data = [
                //"state_id" => $row[4],
                "city_name" => $row[3],
                "locations" => [$locations],
                //"created_at" => $now,
                //"updated_at" => $now,
            ];
            if (isset($row[4]) && $row[4] && (int)$row[4] > 0) {
                if (!isset($data[$row[4]])) { // If the state position has not been created, init that position
                    $data[$row[4]][] = $city_data;
                } else {
                    $index = array_search($row[3], array_column($data[$row[4]], 'city_name'), false);
                    if ($index === false) { // If no entry found, push the whole new city array to the state position
                        $data[$row[4]][] = $city_data;
                    } else { // else if a city with the same name exists within this state, push the new location data
                        $data[$row[4]][$index]["locations"][] = $locations;
                    }
                }
            }
        }
        DB::transaction(function () use ($data) {
            foreach ($data as $state_id => $cities) {
                foreach ($cities as $item) {
                    $city = new City();
                    $city->state_id = $state_id;
                    $city->name = $item["city_name"];
                    $city->save();

                    $city->locations()->saveMany($item["locations"]);
                }
            }
        });
    }
}
