<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Drivers\ProfileResource;
use Illuminate\Http\Request;

class ProfileController extends Controller
{

    public function getProfile()
    {
        $driver = auth()->user();

        return response(new ProfileResource($driver), 200);
    }

}
