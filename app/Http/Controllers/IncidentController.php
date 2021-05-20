<?php

namespace App\Http\Controllers;

use App\Models\Incident;
use App\Models\IncidentType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class IncidentController extends Controller
{

    /**
     * @param array $data
     * @param int|null $id
     * @return \Illuminate\Contracts\Validation\Validator
     */
    private function validator(array $data, int $id = null)
    {
        return Validator::make($data, [
            'incident_type_id' => ['required', 'exists:incident_types,id'],
            'driver_id' => ['required', 'exists:drivers,id'],
            'sanction' => ['required'],
            'safety_description' => ['required', 'string', 'max:512'],
            'driver_description' => ['required', 'string', 'max:512'],
        ]);
    }

    /**
     * @return array
     */
    private function createEditParams(): array
    {
        return [
            'incident_types' => [null => ''] + IncidentType::select(DB::raw("IF(fine IS NOT NULL, CONCAT(name, ' - ', CONCAT('$', FORMAT(fine, 2))), name) as text"), 'id')
                    ->pluck('text', 'id')
                    ->toArray(),
            'sanctions' => [null => '', 'warning' => 'Warning', 'fine' => 'Fine', 'firing' => 'Firing'],
        ];
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('incidents.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $params = $this->createEditParams();
        return view('incidents.create', $params);
    }

    /**
     * @param Request $request
     * @param null $id
     * @return Incident
     */
    private function storeUpdate(Request $request, $id = null): Incident
    {
        if ($id)
            $incident = Incident::find($id);
        else {
            $incident = new Incident();
            $incident->user_id = auth()->user()->id;
        }

        $incident->incident_type_id = $request->incident_type_id;
        $incident->driver_id = $request->driver_id;
        $incident->sanction = $request->sanction;
        $incident->safety_description = $request->safety_description;
        $incident->driver_description = $request->driver_description;
        $incident->save();

        return $incident;
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validator($request->all())->validate();

        $this->storeUpdate($request);

        return redirect()->route('incident.index');
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
        $incident = Incident::find($id);
        $params = compact('incident') + $this->createEditParams();
        return view('incidents.edit', $params);
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
        $this->validator($request->all())->validate();

        $this->storeUpdate($request, $id);

        return redirect()->route('incident.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $incident = Incident::find($id);

        if ($incident)
            return ['success' => $incident->delete()];
        else
            return ['success' => false];
    }
}
