<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Helpers\KeyValueResource;
use App\Models\LoadDescription;
use Illuminate\Http\Request;

class LoadDescriptionController extends Controller
{
    public function getLoadDescriptions()
    {
        $language = auth()->user()->language ?? 'english';
        $select = ['text as key'];
        switch ($language) {
            case 'english':
            default:
                $select[] = 'name as value';
                break;
            case 'spanish':
                $select[] = 'name_spanish as value';
                break;
        }
        $descriptions = LoadDescription::select($select)
            //->where('broker_id', auth()->user()->broker_id)
            ->get();

        return response([
            'status' => 'ok',
            'message' => 'Found load descriptions',
            'loadDescriptions' => KeyValueResource::collection($descriptions),
        ]);
    }
}
