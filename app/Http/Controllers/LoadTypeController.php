<?php

namespace App\Http\Controllers;

use App\Models\LoadType;
use App\Traits\CRUD\crudMessage;
use App\Traits\EloquentQueryBuilder\GetSelectionData;
use App\Traits\EloquentQueryBuilder\GetSimpleSearchData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LoadTypeController extends Controller
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
            'shipper' => ['sometimes', 'exists:shippers,id'],
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('loadTypes.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('loadTypes.create');
    }

    /**
     * @param Request $request
     * @param null $id
     * @return LoadType
     */
    private function storeUpdate(Request $request, $id = null): LoadType
    {
        if ($id)
            $loadType = LoadType::findOrFail($id);
        else {
            $loadType = new LoadType();
            $loadType->shipper_id = auth()->guard('shipper')->check() ? auth()->user()->id : $request->shipper;
        }

        $loadType->name = $request->name;
        $loadType->save();

        return $loadType;
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

        $loadType = $this->storeUpdate($request);

        if ($request->ajax())
            return ['success' => true, 'data' => $loadType];

        return redirect()->route('loadType.index');
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
        $loadType = LoadType::findOrFail($id);
        $params = compact('loadType');
        return view('loadTypes.edit', $params);
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

        return redirect()->route('loadType.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int|null $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, int $id = null)
    {
        if (!$id)
            $id = $request->id;
        $loadType = LoadType::findOrFail($id);

        if ($loadType) {
            $message = '';
            if ($loadType->loads()->first())
                $message .= "•" . $this->generateCrudMessage(4, 'Load Type', ['constraint' => 'loads']) . "<br>";
            if ($message)
                return ['success' => false, 'msg' => $message];
            else
                return ['success' => $loadType->delete()];
        } else
            return ['success' => false];
    }

    /**
     * @param Request $request
     * @return array
     */
    public function selection(Request $request): array
    {
        $shipper = auth()->guard('shipper')->check() ? auth()->user()->id : $request->shipper;
        $query = LoadType::select([
            'id',
            'name as text',
        ])
            ->where("name", "LIKE", "%$request->search%")
            ->where('shipper_id', $shipper);

        return $this->selectionData($query, $request->take, $request->page);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request)
    {
        $query = LoadType::select([
            "load_types.id",
            "load_types.name",
        ]);

        return $this->multiTabSearchData($query, $request);
    }
}
