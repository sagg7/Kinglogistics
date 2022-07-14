<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Helpers\KeyValueResource;
use App\Models\Origin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OriginController extends Controller
{
    public function getOrigins()
    {
        $origins = Origin::select([
            'id as key',
            'name as value',
        ])
            ->where('broker_id', auth()->user()->broker_id)
            ->get();

        return response([
            'status' => 'ok',
            'message' => 'Found origins',
            'origins' => KeyValueResource::collection($origins),
        ]);
    }

    private function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
        ]);
    }

    public function storeOrigin(Request $request)
    {
        $this->validator($request->all())->validate();

        $origin = new Origin();
        $origin->broker_id = auth()->user()->broker_id;
        $origin->name = $request->name;

        if ($request->latitude && $request->longitude) {
            $coords = "$request->latitude,$request->longitude";
        } else {
            $coords = "0,0";
        }
        $origin->coords = $coords;
        $origin->save();

        return response([
            'status' => 'ok',
            'message' => 'Origin created successfully',
            'origin' => $origin,
        ]);
    }
}
