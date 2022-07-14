<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Helpers\KeyValueResource;
use App\Models\Destination;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DestinationController extends Controller
{
    public function getDestinations()
    {
        $destinations = Destination::select([
            'id as key',
            'name as value',
        ])
            ->where('broker_id', auth()->user()->broker_id)
            ->get();

        return response([
            'status' => 'ok',
            'message' => 'Found destinations',
            'destinations' => KeyValueResource::collection($destinations),
        ]);
    }

    private function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'status' => ['required'],
            'status_current' => ['numeric'],
            'status_total' => ['numeric'],
        ]);
    }

    public function storeDestination(Request $request)
    {
        $this->validator($request->all())->validate();

        $destination = new Destination();
        $destination->broker_id = auth()->user()->broker_id;
        $destination->name = $request->name;
        $destination->status = $request->status;
        $destination->status_current = $request->status_current;
        $destination->status_total = $request->status_total;

        if ($request->latitude && $request->longitude) {
            $coords = "$request->latitude,$request->longitude";
        } else {
            $coords = "0,0";
        }
        $destination->coords = $coords;
        $destination->save();

        return response([
            'status' => 'ok',
            'message' => 'Destination created successfully',
            'destination' => $destination,
        ]);
    }
}
