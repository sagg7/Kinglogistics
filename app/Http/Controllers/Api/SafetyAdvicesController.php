<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Driver;
use App\Notifications\SafetyAdvice;
use Illuminate\Http\Request;

// TODO: Remove this Controller, is just for demo purposes
class SafetyAdvicesController extends Controller
{

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
