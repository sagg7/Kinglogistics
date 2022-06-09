<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Helpers\IdNameResource;
use App\Http\Resources\Helpers\KeyValueResource;
use App\Models\AppConfig;
use App\Models\BoxType;
use App\Models\BrokerAppConfig;
use App\Models\ChassisType;

class DashboardController extends Controller
{
    private function setKeyValueConfig($data)
    {
        $array = [];
        foreach ($data as $key => $item) {
            $array[] = [
                'key' => $key,
                'value' => $item,
            ];
        }
        return $array;
    }

    public function appBootstrap()
    {
        $broker_id = auth()->user()->broker_id;
        $chassisTypes = ChassisType::all();
        $boxTypes = BoxType::all();
        $appConfigurations = AppConfig::all();
        $appBrokerConfigurations = $this->setKeyValueConfig(BrokerAppConfig::select('disable_boxes')->where('broker_id', $broker_id)->first()->toArray());

        return response([
            'chassis_types' => IdNameResource::collection($chassisTypes),
            'box_types' => IdNameResource::collection($boxTypes),
            'configurations' => KeyValueResource::collection($appConfigurations),
            'broker_configurations' => array_merge([['key' => 'broker_id', 'value' => $broker_id]], $appBrokerConfigurations),
        ]);

    }

}
