<?php

namespace App\Imports;


use App\Models\Income;
use App\Models\IncomeAccount;
use App\Models\IncidentType;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToArray;


class IncomeImport implements ToArray
{
    public $data;

    private function validator($data)
    {
        return Validator::make($data, [
            'type_id' => ['required', 'exists:income_types,id'],
            'account_id' => ['required', 'exists:income_accounts,id'],
            'amount' => ['required', 'numeric'],
            'description' => ['required', 'string', 'max:512'],
            'note' => ['string', 'max:512'],
        ]);
    }

    /**
     * @param array $array
     */
    public function array(array $array)
    {
        $accounts = IncomeAccount::whereHas('broker', function ($q) {
            $q->where('id', session('broker'));
        })->get();
        $types = IncidentType::whereHas('broker', function ($q) {
            $q->where('id', session('broker'));
        })->get();
        $account_id = 0;
        $type_id = 0;
        $formatted = [];
        $errors = [];
        $headers = [];
        $now = Carbon::now();
        foreach ($array as $idx => $row) {
           foreach($accounts as $account){
               if($row[2]==$account->name){
                $account_id = $account->id ;
                break 1;
               }
           }
           foreach($types as $type){
               if($row[1]==$type->name){
                $type_id = $type->id;
                break 1;
               }
           }
                    $toSubmit = [
                        "type_id" => $type_id,
                        "date"=> $row[0],
                        "account_id" => $account_id,
                        "amount" => $row[3],
                        "description" => $row[4],
                        "note" => $row[5],
                    ];
                    $valErrors = $this->validator($toSubmit)->errors()->all();
                
                if (count($valErrors) > 0) {
                    $errorString = "";
                    foreach ($valErrors as $error) {
                        $errorString .= "$error\n";
                    }
                    $errors[] = array_merge($row, [$errorString]);
                } else {
                    $formatted[] = $toSubmit;
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
            Income::insert($formatted);
        });
    }
}
