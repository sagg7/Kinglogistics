<?php

namespace App\Imports;

use App\Exports\DieselErrorsExport;
use App\Models\Diesel;
use App\Models\Truck;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToArray;

class DieselImport implements ToArray
{
    public $data;

    private function validator($data)
    {
        return Validator::make($data, [
            'card' => ['required', 'string', 'max:255'],
            'date' => ['required', 'date'],
            'odometer' => ['required', 'string', 'max:255'],
            'location' => ['required', 'string', 'max:255'],
            'fees' => ['required', 'numeric'],
            'price' => ['required', 'numeric'],
            'quantity' => ['required', 'numeric'],
            'discount' => ['required', 'numeric'],
            'amount' => ['required', 'numeric'],
        ]);
    }

    /**
     * @param array $array
     */
    public function array(array $array)
    {
        $formatted = [];
        $errors = [];
        $headers = [];
        $now = Carbon::now();
        foreach ($array as $idx => $row) {
            if ($idx > 0) {
                $truck = Truck::where('diesel_card', substr($row[0], 1))->first();
                if ($truck) {
                    $toSubmit = [
                        "truck_id" => $truck->id,
                        "card" => $row[0],
                        "date" => $row[1],
                        "odometer" => $row[5],
                        "location" => $row[6],
                        "fees" => $row[9],
                        "price" => $row[11],
                        "quantity" => $row[14],
                        "discount" => $row[15],
                        "amount" => $row[17],
                        "created_at" => $now,
                        "updated_at" => $now,
                    ];
                    $valErrors = $this->validator($toSubmit)->errors()->all();
                } else {
                    $valErrors = ['No truck with this card number was found.'];
                }
                if (count($valErrors) > 0) {
                    $errorString = "";
                    foreach ($valErrors as $error) {
                        $errorString .= "$error\n";
                    }
                    $errors[] = array_merge($row, [$errorString]);
                } else {
                    $formatted[] = $toSubmit;
                }
            } else {
                $headers[] = array_merge($row, ['Errors']);
            }
        }

        $result = [
            'data' => $formatted,
            'errors' => null,
        ];

        if (count($errors) > 0) {
            $result['errors'] = array_merge($headers, $errors);
        }

        $this->data = $result;

        DB::transaction(function () use ($formatted) {
            Diesel::insert($formatted);
        });
    }
}
