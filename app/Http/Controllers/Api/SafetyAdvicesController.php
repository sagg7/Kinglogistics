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
        $driver = Driver::find($request->driver_id);

        $driver->notify(new SafetyAdvice());

    }

}
