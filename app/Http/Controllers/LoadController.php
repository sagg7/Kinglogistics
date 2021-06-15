<?php

namespace App\Http\Controllers;

use App\Models\AvailableDriver;
use App\Models\Load;
use App\Models\LoadLog;
use App\Models\Shipper;
use App\Traits\EloquentQueryBuilder\GetSelectionData;
use App\Traits\EloquentQueryBuilder\GetSimpleSearchData;
use App\Traits\Load\GenerateLoads;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LoadController extends Controller
{
    use GenerateLoads, GetSelectionData, GetSimpleSearchData;

    /**
     * @return array
     */
    private function createEditParams(): array
    {
        return [
            'shippers' => [null => 'Select'] + Shipper::skip(0)->take(15)->pluck('name', 'id')->toArray(),
        ];
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->all();
        $data['date'] = $request->date_submit;

        $shipper = auth()->guard('shipper') ? auth()->guard()->user()->id : $request->shipper_id;
        $data['shipper_id'] = $shipper;

        $this->validator($data)->validate();

        $drivers = AvailableDriver::with('driver')
            ->whereHas('driver', function ($q) use ($shipper) {
                // Filter users by current Turn, check if is morning first else night
                $now = Carbon::now();
                if ($now->between(Carbon::createFromTimeString('6:00'), Carbon::createFromTimeString('17:59')))
                    $q->where('turn_id', 0);
                else
                    $q->where('turn_id', 1);
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
                    })
                        // OR WHERE THE PIVOT TABLE OF THE DRIVER AND SHIPPER HAS EXISTING RELATIONSHIP
                        ->orWhereHas('shippers', function ($s) use ($shipper) {
                            $s->where('id', $shipper);
                        });
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
            for ($i = 0; $i < $request->load_number; $i++) {
                // Assign available drivers to load
                $data['driver_id'] = $drivers[$i]->id ?? null;
                // If driver was assigned, set status as requested, else set status as unallocated to wait for driver
                $data['driver_id'] ? $data['status'] = 'requested' : $data['status'] = 'unallocated';

                $this->storeUpdate($data);
            }
        });

        return redirect()->route('load.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Load  $load
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
        return abort(404);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id)
    {
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
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request)
    {
        $query = Load::select([
            "loads.id",
            "loads.date",
            "loads.control_number",
            /*"loads.customer_po",
            "loads.customer_reference",*/
            "loads.origin",
            "loads.destination",
            "loads.driver_id",
        ])
            ->with('driver:id,name');

        if ($request->searchable) {
            $searchable = [];
            $statement = "whereHas";
            foreach ($request->searchable as $item) {
                switch ($item) {
                    case 'driver':
                        $query->$statement($item, function ($q) use ($request) {
                            $q->where('name', 'LIKE', "%$request->search%");
                        });
                        $statement = "orWhereHas";
                        break;
                    default:
                        $searchable[] = $item;
                        break;
                }
            }
            $request->searchable = $searchable;
        }

        return $this->simpleSearchData($query, $request);
    }
}
