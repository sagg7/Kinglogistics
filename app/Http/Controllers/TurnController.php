<?php

namespace App\Http\Controllers;

use App\Models\Turn;
use App\Traits\EloquentQueryBuilder\GetSelectionData;
use Illuminate\Http\Request;

class TurnController extends Controller
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
     * @param  \App\Models\Turn  $turn
     * @return \Illuminate\Http\Response
     */
    public function show(Turn $turn)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Turn  $turn
     * @return \Illuminate\Http\Response
     */
    public function edit(Turn $turn)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Turn  $turn
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Turn $turn)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Turn  $turn
     * @return \Illuminate\Http\Response
     */
    public function destroy(Turn $turn)
    {
        //
    }

    /**
     * @param Request $request
     * @return array
     */
    /*public function selection(Request $request)
    {
        $query = Turn::select([
            'turns.id',
            DB::raw("CONCAT(zones.name, ' - ', turns.name) as text"),
        ])
            ->join('zones', 'zones.id', '=', 'turns.zone_id')
            ->where("turns.name", "LIKE", "%$request->search%")
            ->orWhere("zones.name", "LIKE", "%$request->search%");

        return $this->selectionData($query, $request->take, $request->page);
    }*/
}
