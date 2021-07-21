<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Helpers\IdNameResource;
use App\Http\Resources\Helpers\KeyValueResource;
use App\Models\AppConfig;
use App\Models\BoxType;
use App\Models\ChassisType;

class DashboardController extends Controller
{

    public function appBootstrap()
    {
        $chassisTypes = ChassisType::all();
        $boxTypes = BoxType::all();
        $appConfigurations = AppConfig::all();

        return response([
            'chassis_types' => IdNameResource::collection($chassisTypes),
            'box_types' => IdNameResource::collection($boxTypes),
            'configurations' => KeyValueResource::collection($appConfigurations),
        ]);

    }

}
