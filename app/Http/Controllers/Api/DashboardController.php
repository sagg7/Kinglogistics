<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Helpers\IdNameResource;
use App\Http\Resources\Helpers\KeyValueResource;
use App\Models\AppConfig;
use App\Models\BoxType;
use App\Models\BrokerAppConfig;
use App\Models\ChassisType;
use App\Models\Rename;

class DashboardController extends Controller
{
    private function setKeyValueConfig($data)
    {
        $array = [];
        if ($data) {
            foreach ($data->toArray() as $key => $item) {
                $array[] = [
                    'key' => $key,
                    'value' => $item,
                ];
            }
        }
        return $array;
    }

    public function appBootstrap()
    {
        $broker_id = auth()->user()->broker_id;
        $chassisTypes = ChassisType::all();
        $boxTypes = BoxType::all();
        $appConfigurations = AppConfig::all();
        $appBrokerConfigurations = $this->setKeyValueConfig(BrokerAppConfig::select('disable_boxes')->where('broker_id', $broker_id)->first());
        $renames = Rename::where('broker_id', $broker_id)->get([
            'job', 'control_number', 'customer_reference', 'bol', 'tons', 'po', 'carrier',
        ])->toArray();

        return response([
            'chassis_types' => IdNameResource::collection($chassisTypes),
            'box_types' => IdNameResource::collection($boxTypes),
            'configurations' => KeyValueResource::collection($appConfigurations),
            'broker_configurations' => array_merge([['key' => 'broker_id', 'value' => $broker_id]], $appBrokerConfigurations),
            'broker_renames' => $renames,
        ]);

    }

}
