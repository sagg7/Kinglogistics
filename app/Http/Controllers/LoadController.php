<?php

namespace App\Http\Controllers;

use App\Events\LoadUpdate;
use App\Models\AvailableDriver;
use App\Models\Driver;
use App\Models\Load;
use App\Models\LoadLog;
use App\Models\Shipper;
use App\Traits\EloquentQueryBuilder\GetSelectionData;
use App\Traits\EloquentQueryBuilder\GetSimpleSearchData;
use App\Traits\Load\GenerateLoads;
use App\Traits\Turn\DriverTurn;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LoadController extends Controller
{
    use GenerateLoads, GetSelectionData, GetSimpleSearchData, DriverTurn;

    /**
     * @return array
     */
    private function createEditParams(): array
    {
        return [
            'shippers' => [null => 'Select'] + Shipper::pluck('name', 'id')->toArray(),
            'available_drivers' => [null => 'Select'] + Driver::pluck('name', 'id')->toArray(),
        ];
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (auth()->guard('web')->check() && auth()->user()->hasRole('dispatch'))
            return view('loads.indexDispatch');
        else
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
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->all();
        $data['date'] = $request->date_submit;

        $shipper = auth()->guard('shipper')->check() ? auth()->user()->id : $request->shipper_id;
        $data['shipper_id'] = $shipper;

        $this->validator($data)->validate();

        $drivers = AvailableDriver::with('driver')
            ->whereHas('driver', function ($q) use ($shipper) {
                // Filter users by current Turn, check if is morning first else night
                $q->whereHas('turn', function ($r) {
                    $this->filterByActiveTurn($r);
                });
                // The driver must not be inactive
                $q->whereNull('inactive')
                    // and also a truck
                    ->whereHas('truck', function ($r) {
                        // and also a trailer
                        $r->whereHas('trailer');
                    });
                // Then check the relations with the shipper
                $q->where(function ($r) use ($shipper) {
                    // WHERE THE ASSIGNED TRAILER TO THE TRUCK THAT BELONGS TO THE DRIVER IS OWNED BY THE SHIPPER
                    $r->whereHas('truck', function ($s) use ($shipper) {
                        $s->whereHas('trailer', function ($t) use ($shipper) {
                            $t->whereHas('shippers', function ($u) use ($shipper) {
                                $u->where('shipper_id', $shipper);
                            });
                        });
                    });
                });
                // The carrier must be active
                $q->whereHas("carrier", function ($q) {
                    $q->whereNull("inactive");
                });
            })
            ->take($request->load_number)
            ->get();

        DB::transaction(function () use ($request, $data, $drivers) {
            $load_log = new LoadLog();
            $load_log->user_id = auth()->user()->id;
            $load_log->quantity = $request->load_number;
            $load_log->type = auth()->guard('shipper')->check() ? 'shipper' : 'user';
            $load_log->save();
            $data['load_log_id'] = $load_log->id;
            $control_number = (int)$request->control_number;
            for ($i = 0; $i < $request->load_number; $i++) {
                if (isset($request->driver_id)){ //temporary
                    $data['driver_id'] = $request->driver_id;
                    $data['status'] = 'finished';
                } else {
                    // Assign available drivers to load
                    $data['driver_id'] = $drivers[$i]->driver_id ?? null;
                    // If driver was assigned, set status as requested, else set status as unallocated to wait for driver
                    $data['driver_id'] ? $data['status'] = 'requested' : $data['status'] = 'unallocated';
                }
                $data['control_number'] = $control_number;

                $load = $this->storeUpdate($data);

                $control_number++;

                event(new LoadUpdate($load));
            }
        });

        return redirect()->route('load.index');
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Load $load
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
    {
        $load = Load::with('driver', 'shipper')
            ->find($id);
        $params = compact('load') + $this->createEditParams();
        return view('loads.show', $params);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit(int $id)
    {
        $load = Load::with('driver')
            ->with('load_type:id,name', 'trip:id,name')
            ->find($id);
        $params = compact('load') + $this->createEditParams();
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
        /**
         * THERE SHOULD BE NO CASE TO UPDATE
         */
        $data = $request->all();
        $data['date'] = $request->date_submit;
        $this->validator($data)->validate();
        $this->storeUpdate($data, $id);

        return redirect()->route('load.index');
        //return abort(404);
    }

    public function partialUpdate(Request $request, int $id)
    {
        $load = Load::findOrFail($id);
        $load->fill($request->all());
        return $load->update();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id)
    {
        /**
         * TODO: Return drivers to available drivers
         *
         */

        $load = Load::findOrFail($id);

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
     * @param $item
     * @return array|string[]|null
     */
    private function getRelationArray($item): ?array
    {
        switch ($item) {
            case 'driver':
                $array = [
                    'relation' => $item,
                    'column' => 'name',
                ];
                break;
            default:
                $array = null;
        }

        return $array;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request)
    {
        $select = [
            "loads.id",
            "loads.date",
            "loads.control_number",
            /*"loads.customer_po",
            "loads.customer_reference",*/
            "loads.origin",
            "loads.destination",
            "loads.driver_id",
            "loads.status",
        ];
        $query = Load::with('driver:id,name');
        if (!$request->sortModel) {
            $query->orderByDesc('date');
        }
        if (auth()->guard('web')->check() && auth()->user()->hasRole('dispatch')) {
            $query->with('loadStatus:load_id,to_location_voucher,finished_voucher');
            $select[] = 'customer_reference';
            $select[] = 'bol';
        } else {
            if(isset($request->searchable)){
                $array = $request->searchable;
                array_push($array, 'customer_reference');
                array_push($array, 'customer_po');
                array_push($array, 'bol');
                $request->searchable = $array;
            }
        }

        if (str_contains($request->search, 'transit')){
            $request->search = 'location';
        }

        if(strpos($request->search, '/')){
            $date = explode('/', $request->search);
            $request->search = $date[2].'-'.$date[0].'-'.$date[1];
        }
        $query->select($select);

        return $this->multiTabSearchData($query, $request, 'getRelationArray');
    }
}
