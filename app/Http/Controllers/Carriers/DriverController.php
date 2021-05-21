<?php

namespace App\Http\Controllers\Carriers;

use App\Http\Controllers\Controller;
use App\Models\Driver;
use App\Traits\Driver\DriverParams;
use App\Traits\EloquentQueryBuilder\GetSelectionData;
use App\Traits\EloquentQueryBuilder\GetSimpleSearchData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class DriverController extends Controller
{
    use GetSelectionData, GetSimpleSearchData, DriverParams;
    /**
     * @param array $data
     * @param int|null $id
     * @return \Illuminate\Contracts\Validation\Validator
     */
    private function validator(array $data, int $id = null)
    {
        return Validator::make($data, [
            'turn_id' => ['required', 'numeric'],
            'zone_id' => ['required', 'exists:zones,id'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['string', 'email', 'max:255', "unique:drivers,email,$id,id"],
            'password' => [$id ? 'nullable' : 'required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    /**
     * @return array
     */
    private function createEditParams(): array
    {
        return $this->getTurnsArray();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('subdomains.carriers.drivers.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $params = $this->createEditParams();
        return view('subdomains.carriers.drivers.create', $params);
    }

    /**
     * @param Request $request
     * @param null $id
     * @return Driver
     */
    private function storeUpdate(Request $request, $id = null): Driver
    {
        if ($id)
            $driver = Driver::find($id);
        else {
            $driver = new Driver();
            $driver->carrier_id = auth()->user()->id;
        }

        $driver->turn_id = $request->turn_id;
        $driver->zone_id = $request->zone_id;
        $driver->name = $request->name;
        $driver->email = $request->email;
        $driver->inactive = $request->inactive ?? null;
        if ($request->password)
            $driver->password = Hash::make($request->password);
        $driver->save();

        return $driver;
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

        return redirect()->route('driver.index');
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
        $driver = Driver::with(['zone:id,name'])
            ->find($id);
        $params = compact('driver') + $this->createEditParams();
        return view('subdomains.carriers.drivers.edit', $params);
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

        return redirect()->route('driver.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * @param Request $request
     * @return array
     */
    public function selection(Request $request): array
    {
        $query = Driver::select([
            'drivers.id',
            'drivers.name as text',
        ])
            ->where("name", "LIKE", "%$request->search%")
            /*->where("carrier_id", auth()->user()->id)
            ->whereNull("inactive")
            ->with('truck.trailer:id,number')*/;

        return $this->selectionData($query, $request->take, $request->page);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request)
    {
        $query = Driver::select([
            "drivers.id",
            "drivers.name",
            "drivers.zone_id",
        ])
            ->with('truck:driver_id,number', 'zone:id,name');

        if ($request->searchable) {
            $searchable = [];
            $statement = "whereHas";
            foreach ($request->searchable as $item) {
                switch ($item) {
                    case 'zone':
                        $query->$statement($item, function ($q) use ($request) {
                            $q->where('name', 'LIKE', "%$request->search%");
                        });
                        $statement = "orWhereHas";
                        break;
                    case 'truck':
                        $query->$statement($item, function ($q) use ($request) {
                            $q->where('number', 'LIKE', "%$request->search%");
                        });
                        $statement = "orWhereHas";
                        break;
                    default:
                        $searchable[count($searchable) + 1] = $item;
                        break;
                }
            }
            $request->searchable = $searchable;
        }

        return $this->simpleSearchData($query, $request, 'orWhere');
    }
}
