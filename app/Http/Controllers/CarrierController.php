<?php

namespace App\Http\Controllers;

use App\Models\Carrier;
use App\Rules\EmailArray;
use App\Traits\CRUD\crudMessage;
use App\Traits\EloquentQueryBuilder\GetSelectionData;
use App\Traits\EloquentQueryBuilder\GetSimpleSearchData;
use App\Traits\Paperwork\PaperworkFilesFunctions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CarrierController extends Controller
{
    use GetSelectionData, GetSimpleSearchData, PaperworkFilesFunctions, crudMessage;
    /**
     * @param array $data
     * @param int|null $id
     * @return \Illuminate\Contracts\Validation\Validator
     */

    private function validator(array $data, int $id = null)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['string', 'email', 'max:255', "unique:carriers,email,$id,id"],
            'password' => [$id ? 'nullable' : 'required', 'string', 'min:8', 'confirmed'],
            'phone' => ['nullable','string','max:255'],
            'address' => ['nullable','string','max:255'],
            'city' => ['nullable','string','max:255'],
            'state' => ['nullable','string','max:255'],
            'zip_code' => ['nullable','string','max:255'],
            'owner' => ['nullable','string','max:255'],
            'invoice_email' => ['nullable', new EmailArray, 'max:255'],
        ]);
    }

    /**
     * @return array
     */
    private function createEditParams(): array
    {
        return $this->getPaperworkByType("carrier");
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
        $params = $this->createEditParams();
        return view('carriers.create', $params);
    }

    /**
     * @param Request $request
     * @param null $id
     * @return Carrier
     */
    private function storeUpdate(Request $request, $id = null): Carrier
    {
        if ($id)
            $carrier = Carrier::findOrFail($id);
        else
            $carrier = new Carrier();

        $carrier->name = $request->name;
        $carrier->email = $request->email;
        $carrier->phone = $request->phone;
        $carrier->address = $request->address;
        $carrier->city = $request->city;
        $carrier->state = $request->state;
        $carrier->zip_code = $request->zip_code;
        $carrier->owner = $request->owner;
        $carrier->inactive = $request->inactive ?? null;
        $carrier->invoice_email = $request->invoice_email;
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
        $createEdit = $this->createEditParams();
        $paperworkUploads = $this->getFilesPaperwork($createEdit['filesUploads'], $carrier->id);
        $paperworkTemplates = $this->getTemplatesPaperwork($createEdit['filesTemplates'], $carrier->id);
        $params = compact('carrier', 'paperworkUploads', 'paperworkTemplates') + $createEdit;
        return view('carriers.edit', $params);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @param bool $profile
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request, int $id, bool $profile = false)
    {
        $this->validator($request->all(), $id)->validate();

        $this->storeUpdate($request, $id);

        if ($profile)
            return redirect()->route('carrier.profile');
        else
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
        $carrier = Carrier::findOrFail($id);

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
        $query = Carrier::select([
            "carriers.id",
            "carriers.name",
            "carriers.email",
            "carriers.phone",
        ]);

        return $this->multiTabSearchData($query, $request);
    }
}
