<?php

namespace App\Http\Controllers;

use App\Models\Carrier;
use App\Traits\CRUD\crudMessage;
use App\Traits\EloquentQueryBuilder\GetSelectionData;
use App\Traits\EloquentQueryBuilder\GetSimpleSearchData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CarrierController extends Controller
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
            'email' => ['string', 'email', 'max:255', "unique:users,email,$id,id"],
            'password' => [$id ? 'nullable' : 'required', 'string', 'min:8', 'confirmed'],
            'phone' => ['nullable','string','max:255'],
            'address' => ['nullable','string','max:255'],
            'city' => ['nullable','string','max:255'],
            'state' => ['nullable','string','max:255'],
            'zip_code' => ['nullable','string','max:255'],
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('carriers.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('carriers.create');
    }

    /**
     * @param Request $request
     * @param null $id
     * @return Carrier
     */
    private function storeUpdate(Request $request, $id = null): Carrier
    {
        if ($id)
            $carrier = Carrier::find($id);
        else
            $carrier = new Carrier();

        $carrier->name = $request->name;
        $carrier->email = $request->email;
        $carrier->phone = $request->phone;
        $carrier->address = $request->address;
        $carrier->city = $request->city;
        $carrier->state = $request->state;
        $carrier->zip_code = $request->zip_code;
        $carrier->inactive = $request->inactive ?? null;;
        if ($request->password)
            $carrier->password = Hash::make($request->password);
        $carrier->save();

        return $carrier;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validator($request->all())->validate();

        $this->storeUpdate($request);

        return redirect()->route('carrier.index');
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
        $carrier = Carrier::findOrFail($id);
        $params = compact('carrier');
        return view('carriers.edit', $params);
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
        $this->validator($request->all(), $id)->validate();

        $this->storeUpdate($request, $id);

        return redirect()->route('carrier.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $carrier = Carrier::find($id);

        if ($carrier) {
            $message = '';
            if ($carrier->rentals()->first())
                $message .= "•" . $this->generateCrudMessage(4, 'Carrier', ['constraint' => 'rentals']) . "<br>";
            if ($message)
                return ['success' => false, 'msg' => $message];
            else
                return ['success' => $carrier->delete()];
        } else
            return ['success' => false];
    }

    /**
     * @param Request $request
     * @return array
     */
    public function selection(Request $request)
    {
        $query = Carrier::select([
            'id',
            'name as text',
        ])
            ->where("name", "LIKE", "%$request->name%")
            ->whereNull("inactive");

        return $this->selectionData($query, $request->take, $request->page);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request)
    {
        $query = Carrier::select([
            "carriers.id",
            "carriers.name",
            "carriers.email",
            "carriers.phone",
        ]);

        return $this->simpleSearchData($query, $request);
    }
}
