<?php

namespace App\Http\Controllers\Carriers;

use App\Http\Controllers\Controller;
use App\Models\Truck;
use App\Traits\EloquentQueryBuilder\GetSelectionData;
use App\Traits\EloquentQueryBuilder\GetSimpleSearchData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TruckController extends Controller
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
            'number' => ['required', 'string', 'max:255'],
            'plate' => ['nullable', 'string', 'max:255'],
            'vin' => ['nullable', 'string', 'max:255'],
            'make' => ['nullable', 'string', 'max:255'],
            'model' => ['nullable', 'string', 'max:255'],
            'year' => ['nullable', 'numeric', 'max:' . (date('Y') + 1)],
        ]);
    }

    /**
     * @return array
     */
    private function createEditParams(): array
    {
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('subdomains.carriers.trucks.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('subdomains.carriers.trucks.create');
    }

    /**
     * @param Request $request
     * @param null $id
     * @return Truck
     */
    private function storeUpdate(Request $request, $id = null): Truck
    {
        if ($id)
            $truck = Truck::find($id);
        else {
            $truck = new Truck();
            $truck->carrier_id = auth()->user()->id;
        }

        $truck->number = $request->number;
        $truck->plate = $request->plate;
        $truck->vin = $request->vin;
        $truck->make = $request->make;
        $truck->model = $request->model;
        $truck->year = $request->year;
        $truck->inactive = $request->inactive ?? null;
        $truck->save();

        return $truck;
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

        return redirect()->route('truck.index');
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
        $trucks = Truck::find($id);
        $params = compact('trucks');
        return view('subdomains.carriers.trucks.edit', $params);
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

        return redirect()->route('truck.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $truck = Truck::find($id);

        if ($truck) {
            $message = '';
            if ($truck->driver)
                $message .= "•" . $this->generateCrudMessage(4, 'Truck', ['constraint' => 'driver']) . "<br>";
            if ($message)
                return ['success' => false, 'msg' => $message];
            else
                return ['success' => $truck->delete()];
        } else
            return ['success' => false];
    }

    /**
     * @param Request $request
     * @return array
     */
    public function selection(Request $request)
    {
        $query = Truck::select([
            'id',
            'number as text',
        ])
            ->where("number", "LIKE", "%$request->search%")
            ->whereNull("inactive");

        if ($request->type === "drivers") {
            $query->whereDoesntHave('driver');
        }

        return $this->selectionData($query, $request->take, $request->page);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request)
    {
        $query = Truck::select([
            "trucks.id",
            "trucks.number",
            "trucks.plate",
            "trucks.vin",
        ])
            ->with(['driver:id,name']);

        return $this->simpleSearchData($query, $request);
    }
}
