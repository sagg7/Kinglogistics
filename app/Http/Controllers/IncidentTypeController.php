<?php

namespace App\Http\Controllers;

use App\Models\IncidentType;
use App\Traits\CRUD\crudMessage;
use App\Traits\EloquentQueryBuilder\GetSelectionData;
use App\Traits\EloquentQueryBuilder\GetSimpleSearchData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class IncidentTypeController extends Controller
{
    use GetSelectionData, GetSimpleSearchData, crudMessage;

    /**
     * @param array $data
     * @param int|null $id
     * @return \Illuminate\Contracts\Validation\Validator
     */
    private function validator(array $data, int $id = null)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'fine' => ['nullable', 'numeric'],
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('incidentTypes.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('incidentTypes.create');
    }

    /**
     * @param Request $request
     * @param null $id
     * @return IncidentType
     */
    private function storeUpdate(Request $request, $id = null): IncidentType
    {
        if ($id)
            $incidentType = IncidentType::whereHas('broker', function ($q) {
                $q->where('id', session('broker'));
            })
                ->findOrFail($id);
        else {
            $incidentType = new IncidentType();
            $incidentType->broker_id = session('broker');
        }

        $incidentType->name = $request->name;
        $incidentType->fine = $request->fine;
        $incidentType->visible = $request->visible ?? null;
        $incidentType->save();

        return $incidentType;
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

        $incidentType = $this->storeUpdate($request);

        if ($request->ajax()) {
            $incidentType->name = $incidentType->fine ? $incidentType->name . ' - $' . number_format($incidentType->fine, 2) : $incidentType->name;
            return ['success' => true, 'data' => $incidentType];
        }

        return redirect()->route('incidentType.index');
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
        $incidentType = IncidentType::whereHas('broker', function ($q) {
            $q->where('id', session('broker'));
        })
            ->findOrFail($id);
        $params = compact('incidentType');
        return view('incidentTypes.edit', $params);
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

        return redirect()->route('incidentType.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param int|null $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, int $id = null)
    {
        if (!$id)
            $id = $request->id;
        $incidentType = IncidentType::whereHas('broker', function ($q) {
            $q->where('id', session('broker'));
        })
            ->findOrFail($id);

        if ($incidentType) {
            $message = '';
            if ($incidentType->incidents()->first())
                $message .= "???" . $this->generateCrudMessage(4, 'Incident Type', ['constraint' => 'incidents']) . "<br>";
            if ($message)
                return ['success' => false, 'msg' => $message];
            else
                return ['success' => $incidentType->delete()];
        } else
            return ['success' => false];
    }

    /**
     * @param Request $request
     * @return array
     */
    public function selection(Request $request)
    {
        $query = IncidentType::select([
            'id',
            DB::raw("CONCAT(name, ' - ', CONCAT('$', FORMAT(fine, 2))) as text"),
        ])
            ->whereHas('broker', function ($q) {
                $q->where('id', session('broker'));
            })
            ->where("name", "LIKE", "%$request->search%");

        return $this->selectionData($query, $request->take, $request->page);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request)
    {
        $query = IncidentType::select([
            "incident_types.id",
            "incident_types.name",
            "incident_types.fine",
        ])
            ->whereHas('broker', function ($q) {
                $q->where('id', session('broker'));
            });
        if (auth()->guard('shipper')->check())
            $query->where('shipper_id', auth()->user()->id);

        return $this->multiTabSearchData($query, $request);
    }
}
