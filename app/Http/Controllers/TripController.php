<?php

namespace App\Http\Controllers;

use App\Models\Trip;
use App\Models\Zone;
use App\Traits\EloquentQueryBuilder\GetSelectionData;
use App\Traits\EloquentQueryBuilder\GetSimpleSearchData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TripController extends Controller
{
    use GetSelectionData, GetSimpleSearchData;

    /**
     * @return array
     */
    private function createEditParams(): array
    {
        return [
            'zones' => [null => ''] + Zone::pluck('name', 'id')->toArray(),
        ];
    }

    private function validator(array $data, int $id = null)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'customer_name' => ['required', 'string', 'max:255'],
            'zone_id' => ['required', 'exists:zones,id'],
            'origin' => ['required', 'string', 'max:255'],
            'origin_coords' => ['required', 'string', 'max:255'],
            'destination' => ['required', 'string', 'max:255'],
            'destination_coords' => ['required', 'string', 'max:255'],
            'mileage' => ['required', 'numeric'],
        ], [
            'origin_coords.required' => 'The origin map location is required',
            'destination_coords.required' => 'The destination map location is required',
        ], [
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('trips.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $params = $this->createEditParams();
        return view('trips.create', $params);
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

        return redirect()->route('trip.index');
    }

    /**
     * @param Request $request
     * @param null $id
     * @return Trip
     */
    private function storeUpdate(Request $request, $id = null): Trip
    {
        if ($id)
            $trip = Trip::findOrFail($id);
        else
            $trip = new Trip();

        $shipper = auth()->guard('shipper') ? auth()->guard()->user()->id : $request->shipper_id;

        $trip->zone_id = $request->zone_id;
        $trip->shipper_id = $shipper;
        $trip->name = $request->name;
        $trip->customer_name = $request->customer_name;
        $trip->origin = $request->origin;
        $trip->origin_coords = $request->origin_coords;
        $trip->destination = $request->destination;
        $trip->destination_coords = $request->destination_coords;
        $trip->mileage = $request->mileage;
        $trip->save();

        return $trip;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Trip  $trip
     * @return \Illuminate\Http\Response
     */
    public function show(Trip $trip)
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
        $trip = Trip::with('zone:id,name', 'shipper:id,name')
            ->where(function ($q) {
                if (auth()->guard('shipper'))
                    $q->where('shipper_id', auth()->guard()->user()->id);
            })
            ->findOrFail($id);
        $params = compact('trip') + $this->createEditParams();
        return view('trips.edit', $params);
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
        $this->validator($request->all())->validate();

        $this->storeUpdate($request, $id);

        return redirect()->route('trip.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id)
    {
        $trip = Trip::findOrFail($id);

        if ($trip) {
            /*$message = '';
            if ($trip->loads()->first())
                $message .= "•" . $this->generateCrudMessage(4, 'Trip', ['constraint' => 'loads']) . "<br>";
            if ($message)
                return ['success' => false, 'msg' => $message];
            else*/
            return ['success' => $trip->delete()];
        } else
            return ['success' => false];
    }

    /**
     * @param Request $request
     * @return array
     */
    public function selection(Request $request)
    {
        $query = Trip::select([
            'id',
            DB::raw("CONCAT(name, ': ', origin, ' - ', destination) as text"),
        ])
            ->where("name", "LIKE", "%$request->search%")
            ->where(function ($q) use ($request) {
                if (auth()->guard('shipper')->check())
                    $q->where('shipper_id', auth()->user()->id());
                else
                    $q->where('shipper_id', $request->shipper);
            });

        return $this->selectionData($query, $request->take, $request->page);
    }

    public function getTrip(Request $request)
    {
        return Trip::findOrFail($request->id);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request, $type)
    {
        $query = Trip::select([
            "trips.id",
            "trips.name",
            "trips.zone_id",
            "trips.shipper_id",
            "trips.origin",
            "trips.destination",
        ])
            ->with([
                'zone:id,name',
                'shipper:id,name',
            ]);

        switch ($type) {
            case 'inactive':
                $query = $query->onlyTrashed();
                break;
            case 'active':
            default:
                break;
        }

        if ($request->searchable) {
            $searchable = [];
            $statement = "whereHas";
            foreach ($request->searchable as $item) {
                switch ($item) {
                    case 'zone':
                    case 'shipper':
                        $query->$statement($item, function ($q) use ($request) {
                            $q->where('name', 'LIKE', "%$request->search%");
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
