<?php

namespace App\Imports;
use App\Models\Expense;
use App\Models\ExpenseAccount;
use App\Models\ExpenseType;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToArray;

class ExpenseImport implements ToArray
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

    private function transformDate($value, $format = 'Y-m-d')
    {
        try {
            return \Carbon\Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value));
        } catch (\ErrorException $e) {
            return \Carbon\Carbon::createFromFormat($format, $value);
        }
    }

    /**
     * @param array $array
     */
    public function array(array $array)
    {
        $accounts = ExpenseAccount::whereHas('broker', function ($q) {
            $q->where('id', session('broker'));
        })->get();
        $types = ExpenseType::whereHas('broker', function ($q) {
            $q->where('id', session('broker'));
        })->get();

        $account_id = 0;
        $type_id = 0;
        $formatted = [];
        $errors = [];
        $headers = [];
        $now = Carbon::now();

        foreach ($array as $key => $row) {
            if($key != 0 ){
                foreach ($accounts as $account) {
                    if (trim(strtolower($row[2])) == trim(strtolower($account->name))) {
                        $account_id = $account->id;
                        break 1;
                    }
                }
                foreach ($types as $type) {
                    if (trim(strtolower($row[1])) == trim(strtolower($type->name))) {
                        $type_id = $type->id;
                        break 1;
                    }
                }

                $toSubmit = [
                    "type_id" => $type_id,
                    "date" => $this->transformDate($row[0])->format('Y-m-d'),
                    "account_id" => $account_id,
                    "amount" => $row[3],
                    "description" => $row[4],
                    "note" => $row[5],
                    "user_id"=>  auth()->user()->id,                       
                    "created_at" => $now,
                    "updated_at" => $now,
                    "broker_id" => session('broker')
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
            Expense::insert($formatted);
        });
    }
}
