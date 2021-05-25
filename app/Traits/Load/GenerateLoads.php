<?php

namespace App\Traits\Load;

use App\Models\Load;
use App\Models\LoadType;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

trait GenerateLoads
{
    /**
     * @param array $data
     * @param int|null $id
     * @return \Illuminate\Contracts\Validation\Validator
     */
    private function validator(array $data, int $id = null)
    {
        return Validator::make($data, [
            'load_type_id' => ['required', 'exists:load_types,id'],
            'driver_id' => ['required', 'exists:drivers,id'],
            'date' => ['required', 'date'],
            'control_number' => ['required', 'string', 'max:255'],
            'origin' => ['required', 'string', 'max:255'],
            'origin_coords' => ['required', 'string', 'max:255'],
            'destination' => ['required', 'string', 'max:255'],
            'destination_coords' => ['required', 'string', 'max:255'],
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_po' => ['required', 'string', 'max:255'],
            'customer_reference' => ['required', 'string', 'max:255'],
            'sand_type' => ['nullable', 'string', 'max:255'],
            'tons' => ['nullable', 'string', 'max:255'],
            'silo_number' => ['nullable', 'string', 'max:255'],
            'container' => ['nullable', 'string', 'max:255'],
            'weight' => ['nullable', 'numeric'],
            'mileage' => ['nullable', 'numeric'],
            'status' => ['required'],
        ]);
    }

    private function createEditParams()
    {
        return [
            'load_types' => [null => 'Select'] + LoadType::pluck('name', 'id')->toArray(),
        ];
    }

    /**
     * @param array $data
     * @param int|null $id
     * @return Load
     */
    private function storeUpdate(array $data, int $id = null): Load
    {
        if ($id)
            $load = Load::find($id);
        else
            $load = new Load();

        $load->load_type_id = $data["load_type_id"];
        $load->driver_id = $data["driver_id"];
        $load->date = Carbon::parse($data["date"]);
        $load->control_number = $data["control_number"];
        $load->origin = $data["origin"];
        $load->origin_coords = $data["origin_coords"];
        $load->destination = $data["destination"];
        $load->destination_coords = $data["destination_coords"];
        $load->customer_name = $data["customer_name"];
        $load->customer_po = $data["customer_po"];
        $load->customer_reference = $data["customer_reference"];
        $load->sand_type = $data["sand_type"];
        $load->tons = $data["tons"];
        $load->silo_number = $data["silo_number"];
        $load->container = $data["container"];
        $load->weight = $data["weight"];
        $load->mileage = $data["mileage"];
        $load->status = $data["status"];
        $load->save();

        return $load;
    }
}
