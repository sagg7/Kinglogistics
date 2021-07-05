<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\Driver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        // Check email
        $driver = Driver::where('email', $request->email)->first();
        // Check password
        if (!$driver || !Hash::check($request->password, $driver->password)) {
            return response(["message" => "Access Denied"], 401);
        }

        $deviceToken = $request->get('device_token');

        if (!empty($deviceToken) && empty(Device::where('token', $deviceToken)->first())) {
            $driver->devices()->create([
                'token' => $deviceToken
            ]);
        }

        $token = $driver->createToken($driver->name . $driver->last_name)->plainTextToken;

        $response = [
            "user" => $driver,
            "token" => $token,
        ];

        return response($response, 201);
    }

    public function logout(Request $request)
    {
        auth()->user()->tokens()->delete();

        return ["message" => "Logged out"];
    }
}
