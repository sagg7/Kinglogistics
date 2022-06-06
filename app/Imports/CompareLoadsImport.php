<?php

namespace App\Imports;


use App\Models\Load;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToArray;

class CompareLoadsImport implements ToArray
{
    public $data;
    public $shipper;
    public $finishedDate;
    public $acceptedDate;


    public function __construct($dateRangeVar, $shipperVar)
    {
        $this->shipper = $shipperVar;
        $dates = explode(" - ", $dateRangeVar);
        $this->acceptedDate = Carbon::parse($dates[0]);
        $this->finishedDate = Carbon::parse($dates[1]);
    }

    
    

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
        $loads = Load::select("loads.id", "control_number")
        ->whereHas('broker', function ($q) {
            $q->where('id', session('broker'));
        })
        ->join('load_statuses', 'load_id', '=', 'loads.id')
        ->whereBetween(DB::raw('IF(finished_timestamp IS NULL,date,finished_timestamp)'), [$this->acceptedDate, $this->finishedDate])
        ->where('shipper_id',$this->shipper)
        ->whereNull('loads.deleted_at')
        ->get();
        // dd(count($loads));
        $columnMatch = array();
        $idLoads = array();
        $firtsLabel = true;
        $cart = array();
        $formatted = [];
        $errors = [];
        $headers = [];
        $now = Carbon::now();
        $count = 0;
        $count2 = 0;
        // dd($loads);
        foreach ($array as $key => $row) {
            //this is to remove the first row of xls uploaded Header
            if ($firtsLabel == true) {
                $firtsLabel = false;
                unset($array[$key]);
            }
        
            foreach ($loads as $keyLoad => $load) {
            
                if (strval($row[0]) == $load->control_number || preg_replace("-", "", strval($row[5])) == preg_replace("-", "", $load->bol)) {
                    $columnMatch[] = $row;
                    unset($array[$key]);
                    unset($loads[$keyLoad]);
                    break 1;
                }
            }
        }

        foreach ($loads as $load) {
                $idLoads[] = $load->id;
        }
        //dd($columnMatch,$array, $loads);

        $formatted[] = [$columnMatch, $array, $idLoads];
        $result = [
            'columnsMatched' => $columnMatch,
            'external' => $array,
            'idLoadsInternal' =>$idLoads,
            'errors' => null,
        ];

        if (count($errors) > 0) {
            $result['errors'] = array_merge($headers, $errors);
        }

        $this->data = $result;
    }
}
