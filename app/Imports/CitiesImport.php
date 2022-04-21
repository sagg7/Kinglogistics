<?php

namespace App\Imports;

use App\Models\City;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\WithChunkReading;

// If you need to run it again, dispatch it as a queue, see the laravel queue documentation for further reference:
// @link https://laravel.com/docs/9.x/queues

class CitiesImport implements ToArray, WithChunkReading, ShouldQueue
{
    public function chunkSize(): int
    {
        return 1000;
    }

    /**
    * @param Collection $collection
    */
    public function array(array $array)
    {
        $data = [];
        $now = Carbon::now();
        foreach ($array as $row) {
            if (isset($row[4]) && $row[4] && (int)$row[4] > 0) {
                $data[] = [
                    "state_id" => $row[4],
                    "name" => $row[3],
                    "latitude" => $row[1],
                    "longitude" => $row[2],
                    "zipcode" => $row[0],
                    "created_at" => $now,
                    "updated_at" => $now,
                ];
            }
        }
        DB::transaction(function () use ($data) {
            City::insert($data);
        });
    }
}
