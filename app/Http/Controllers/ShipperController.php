<?php

namespace App\Http\Controllers;

use App\Models\Shipper;
use App\Traits\EloquentQueryBuilder\GetSelectionData;
use App\Traits\EloquentQueryBuilder\GetSimpleSearchData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ShipperController extends Controller
{
    use GetSelectionData, GetSimpleSearchData;

    /**
     * @param array $data
     * @param int|null $id
     * @return \Illuminate\Contracts\Validation\Validator
     */
    private function validator(array $data, int $id = null)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255', "unique:shippers,email,$id,id"],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'invoice_email' => ['nullable', 'string', 'email', 'max:255'],
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('shippers.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('shippers.create');
    }

    /**
     * @param Request $request
     * @param null $id
     * @return Shipper
     */
    private function storeUpdate(Request $request, $id = null): Shipper
    {
        if ($id)
            $shipper = Shipper::findOrFail($id);
        else
            $shipper = new Shipper();

        $shipper->name = $request->name;
        $shipper->email = $request->email;
        $shipper->invoice_email = $request->invoice_email;
        if ($request->password)
            $shipper->password = Hash::make($request->password);
        $shipper->save();

        return $shipper;
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

        return redirect()->route('shipper.index');
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
        $shipper = Shipper::findOrFail($id);
        $params = compact('shipper');
        return view('shippers.edit', $params);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, int $id)
    {
        $this->validator($request->all())->validate();

        $this->storeUpdate($request, $id);

        return redirect()->route('shipper.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id)
    {
        $shipper = Shipper::findOrFail($id);

        if ($shipper)
            return ['success' => $shipper->delete()];
        else
            return ['success' => false];
    }

    /**
     * @param Request $request
     * @return array
     */
    public function selection(Request $request)
    {
        $query = Shipper::select([
            'id',
            'name as text',
        ])
            ->where("name", "LIKE", "%$request->search%");

        return $this->selectionData($query, $request->take, $request->page);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request)
    {
        $query = Shipper::select([
            "shippers.id",
            "shippers.name",
            "shippers.email",
        ]);

        return $this->simpleSearchData($query, $request);
    }
}
