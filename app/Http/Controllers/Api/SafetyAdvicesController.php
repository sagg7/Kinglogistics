<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Drivers\SafetyAdviceResource;
use App\Models\Driver;
use App\Notifications\SafetyAdvice;
use Illuminate\Http\Request;

// TODO: Remove this Controller, is just for demo purposes
class SafetyAdvicesController extends Controller
{

    public function find($id)
    {
        $driver = auth()->user();

        $safetyMessage = $driver->safetyMessages->firstWhere('id', $id);

        if (!$safetyMessage) {
            return response(['status' => 'error', 'message' => 'Safety advice not found'], 404);
        }

        return response([
            'status' => 'ok',
            'message' => 'Safety advice found',
            'data' => new SafetyAdviceResource($safetyMessage),
        ]);
    }

    public function sendAdvice(Request $request)
    {
        if (isset($request->driver_id)) {
            $driver = Driver::find($request->driver_id);
            $driver->notify(new SafetyAdvice($driver, $request->advice));
        } else {
            $driver = Driver::all();
        }


        return response(['status' => 'ok'], 200);
    }

}
