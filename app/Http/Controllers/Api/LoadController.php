<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Drivers\LoadResource;
use App\Models\Load;
use Illuminate\Http\Request;

class LoadController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $driver = auth()->user();

        $loads = LoadResource::collection($driver->loads);

        return response($loads, 200);
    }

    public function respond(Request $request)
    {
        $driver = auth()->user();
        $load = Load::find($request->load_id);


    }


}
