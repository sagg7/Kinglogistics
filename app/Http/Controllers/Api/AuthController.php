<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
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
        $user = Driver::where('email', $request->email)->first();
        // Check password
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response(["message" => "Access Denied"], 401);
        }

        $token = $user->createToken($user->name.$user->last_name)->plainTextToken;

        $response = [
            "user" => $user,
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
