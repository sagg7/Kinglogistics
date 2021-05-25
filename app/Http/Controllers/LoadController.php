<?php

namespace App\Http\Controllers;

use App\Models\Load;
use App\Traits\EloquentQueryBuilder\GetSelectionData;
use App\Traits\EloquentQueryBuilder\GetSimpleSearchData;
use App\Traits\Load\GenerateLoads;
use Illuminate\Http\Request;

class LoadController extends Controller
{
    use GenerateLoads, GetSelectionData, GetSimpleSearchData;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('loads.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $params = $this->createEditParams();
        return view('loads.create', $params);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->all();
        $data['date'] = $request->date_submit;

        $this->validator($data)->validate();

        $this->storeUpdate($data);

        return redirect()->route('load.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Load  $load
     * @return \Illuminate\Http\Response
     */
    public function show(Load $load)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit(int $id)
    {
        $load = Load::find($id);
        $params = compact('load');
        return view('loads.edit', $params);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request, int $id)
    {
        $data = $request->all();
        $data['date'] = $request->date_submit;

        $this->validator($data)->validate();

        $this->storeUpdate($data, $id);

        return redirect()->route('load.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id)
    {
        $load = Load::find($id);

        if ($load)
            return ['success' => $load->delete()];
        else
            return ['sucess' => false];
    }

    /**
     * @param Request $request
     * @return array
     */
    public function selection(Request $request)
    {
        $query = Load::select([
            'id',
            'name as text',
        ])
            ->where("name", "LIKE", "%$request->search%")
            ->whereNull("inactive");

        return $this->selectionData($query, $request->take, $request->page);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request)
    {
        $query = Load::select([
            "loads.id",
        ]);

        return $this->simpleSearchData($query, $request);
    }
}
