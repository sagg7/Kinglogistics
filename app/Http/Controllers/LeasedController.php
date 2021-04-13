<?php

namespace App\Http\Controllers;

use App\Models\InspectionCategory;
use App\Models\Leased;
use Illuminate\Http\Request;

class LeasedController extends Controller
{
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
     * @param  \App\Models\Leased  $leased
     * @return \Illuminate\Http\Response
     */
    public function show(Leased $leased)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Leased  $leased
     * @return \Illuminate\Http\Response
     */
    public function edit(Leased $leased)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Leased  $leased
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Leased $leased)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Leased  $leased
     * @return \Illuminate\Http\Response
     */
    public function destroy(Leased $leased)
    {
        //
    }

    public function getLeased(Request $request){
        $resultsPerPage = $request->input('resultsPerPage', 15);
        $currentPage = $request->input('page', 0);
        $skip = $resultsPerPage * $currentPage;
        $leased = Leased::select('id','name', 'email', 'phone', 'address');
        if (!empty($request->search))
            $leased->orWhere("name", "LIKE", "%$request->search%")
            ->orWhere("email", "LIKE", "%$request->search%")
            ->orWhere("phone", "LIKE", "%$request->search%")
            ->orWhere("address", "LIKE", "%$request->search%");
        $total = $leased->count();
        $result = $leased->skip(0)->take($resultsPerPage)->get();
        $data = [
            'data' => $result,
            'total' => $total
        ];
        return response()->json($data);
    }
}
