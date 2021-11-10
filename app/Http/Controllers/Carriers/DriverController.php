<?php

namespace App\Http\Controllers\Carriers;

use App\Http\Controllers\Controller;
use App\Models\Driver;
use App\Traits\EloquentQueryBuilder\GetSelectionData;
use App\Traits\EloquentQueryBuilder\GetSimpleSearchData;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class DriverController extends Controller
{
    use GetSelectionData, GetSimpleSearchData;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index()
    {
        return view('subdomains.carriers.drivers.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        //
    }

    /**
     * @param Request $request
     * @return array
     */
    public function selection(Request $request): array
    {
        $query = Driver::select([
            'drivers.id',
            'drivers.name as text',
        ])
            ->where('carrier_id', auth()->user()->id)
            ->where("name", "LIKE", "%$request->search%")
            ->whereNull("inactive")
            ->whereDoesntHave("truck");

        return $this->selectionData($query, $request->take, $request->page);
    }

    /**
     * @param $item
     * @return array|string[]|null
     */
    private function getRelationArray($item): ?array
    {
        switch ($item) {
            case 'zone':
                $array = [
                    'relation' => $item,
                    'column' => 'name',
                ];
                break;
            case 'truck':
                $array = [
                    'relation' => $item,
                    'column' => 'number',
                ];
                break;
            default:
                $array = null;
                break;
        }
        return $array;
    }

    /**
     * @param Request $request
     * @return array
     */
    public function search(Request $request): array
    {
        $query = Driver::select([
            "drivers.id",
            "drivers.name",
            "drivers.zone_id",
        ])
            ->where('carrier_id', auth()->user()->id)
            ->with('truck:driver_id,number', 'zone:id,name');

        return $this->multiTabSearchData($query, $request, 'getRelationArray');
    }
}
