<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Traits\EloquentQueryBuilder\GetSelectionData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CityController extends Controller
{
    use GetSelectionData;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function selection(Request $request)
    {
        $query = City::select([
            'cities.id',
            DB::raw("CONCAT(cities.name, ', ', states.abbreviation) AS text")
        ])
            ->join('states', 'states.id', '=', 'cities.state_id')
            ->where(function ($q) use ($request) {
                $q->where("cities.name", "LIKE", "%$request->search%")
                    ->orWhere("states.name", "LIKE", "%$request->search%");
                    $q->orWhereHas('locations', function ($q) use ($request) {
                        $q->where("zipcode", "LIKE", "%$request->search%");
                    });
            })
            ->with([
                'locations' => function ($q) use ($request) {
                    $q->select('city_id','latitude','longitude');
                    if (strlen($request->search) === 5 && is_numeric($request->search)) {
                        $q->where('zipcode', $request->search);
                    }
                }
            ]);

        return $this->selectionData($query, $request->take, $request->page);
    }
}
