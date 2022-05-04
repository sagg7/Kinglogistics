<?php

namespace App\Http\Controllers;

use App\Models\Shipper;
use App\Rules\EmailArray;
use App\Traits\EloquentQueryBuilder\GetSelectionData;
use App\Traits\EloquentQueryBuilder\GetSimpleSearchData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ShipperController extends Controller
{
    use GetSelectionData, GetSimpleSearchData;

    /**
     * @return array
     */
    private function createEditParams(): array
    {
        return [
            'weekdays' => ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
        ];
    }

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
            'invoice_email' => ['nullable', new EmailArray, 'max:255'],
            'loads_per_invoice' => ['nullable','numeric'],
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
        $params = $this->createEditParams();
        return view('shippers.create', $params);
    }

    /**
     * @param Request $request
     * @param null $id
     * @return Shipper
     */
    private function storeUpdate(Request $request, $id = null): Shipper
    {
        if ($id) {
            $shipper = Shipper::whereHas('broker', function ($q) {
                if (auth()->guard('shipper')->check()) {
                    $q->where('id', auth()->user()->id);
                } else {
                    $q->where('id', session('broker'));
                }
            })
                ->findOrFail($id);
        } else {
            $shipper = new Shipper();
            $shipper->broker_id = session('broker');
        }

        $shipper->name = $request->name;
        $shipper->email = $request->email;
        $shipper->invoice_email = $request->invoice_email;
        $shipper->trucks_required = $request->trucks_required;
        $shipper->loads_per_invoice = $request->loads_per_invoice;
        $shipper->type_rate = $request->type_rate;
        $shipper->factoring = $request->factoring;
        $shipper->days_to_pay = $request->days_to_pay;
        if ($request->password) {
            $shipper->password = Hash::make($request->password);
        }
        if ($request->payment_days) {
            $shipper->payment_days = implode(',',$request->payment_days);
        }
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
        $shipper = Shipper::whereHas('broker', function ($q) {
            $q->where('id', session('broker'));
        })
            ->findOrFail($id);
        $params = compact('shipper') + $this->createEditParams();
        return view('shippers.edit', $params);
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

        if ($profile) {
            return redirect()->route('shipper.profile');
        } else {
            return redirect()->route('shipper.index');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id)
    {
        $shipper = Shipper::whereHas('broker', function ($q) {
            $q->where('id', session('broker'));
        })
            ->findOrFail($id);

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
        $query = Shipper::select([
            "shippers.id",
            "shippers.name",
            "shippers.email",
        ])
            ->whereHas('broker', function ($q) {
                $q->where('id', session('broker'));
            });

        return $this->multiTabSearchData($query, $request);
    }

    public function shipperStatus(Request $request)
    {
        $start = Carbon::now()->subHour(12);
        $start2 = Carbon::now()->subHour(24);
        $end = Carbon::now();
        $shipper_id = $request->shipper_id;

        $shippersAccepted = Shipper::with(['loadStatus'=> function($q) use ($start,$end,$shipper_id){
            $q->whereBetween('accepted_timestamp', [$start, $end])
                ->orderBy('accepted_timestamp', 'asc');
            if($shipper_id){
                $q->where('shipper_id',$shipper_id);
            }
        }])
        ->withCount(['drivers' => function($q){
            $q->where('status', '!=', 'inactive')
                ->whereHas('truck');
        }]);

        //DailyLoads is working with by 24hrs and in this query you get total loads and total trucks
        $dailyLoads = Shipper::select([
            'shippers.id',
            DB::raw("(select count(loads.id) from loads join load_statuses on loads.id = load_id where finished_timestamp between '$start2' and '$end' and shipper_id = shippers.id and loads.deleted_at is null) as loads"),
            DB::raw("(select count(DISTINCT (driver_id)) from loads join load_statuses on loads.id = load_id where finished_timestamp between '$start2' and '$end' and shipper_id = shippers.id and loads.deleted_at is null ) as trucks"),
        ]);
        
        if($shipper_id){
            $dailyLoads->where('shipper_id',$shipper_id);
        }
        $dailyLoads = $dailyLoads->get();

        if($shipper_id){
            $shippersAccepted->where('id',$shipper_id);
        }

        $shippersFinished = Shipper::with(['loadStatus'=> function($q) use ($start,$end,$shipper_id){
            $q->whereBetween('finished_timestamp', [$start, $end])
                ->orderBy('finished_timestamp', 'asc');
            if($shipper_id){
                $q->where('shipper_id',$shipper_id);
            }
        }]);
        
        if($shipper_id){
            $shippersAccepted->where('id',$shipper_id);
            $shippersFinished->where('id',$shipper_id);
        }

        $shippers = $shippersAccepted->get();
        $shipperAvg= [];
        foreach($shippers as $key => $shipper){
            $totalTime = 0;
            $date = $start;
            $count = 0;

        foreach($shipper->loadStatus as $load){
                if ($date != null){
                    $totalTime += Carbon::parse($load->accepted_timestamp)->diffInMinutes($date);
                }
                $date =$load->accepted_timestamp;
                $count++;
            }
            $truck_required =$shipper->trucks_required;
            $active_drivers = $shipper->drivers_count;
            $truck_required_exist = 0;
            if ($active_drivers > 0) {
                $truck_required_exist = $truck_required ? round($active_drivers * 100 / $truck_required) : 0;
            }

            $shipperAvg[$shipper->id] = ['name'=>$shipper->name,'avg'=>round(($count != 0) ? $totalTime/$count : 0), 'trucks_required'=>$shipper->trucks_required, 'active_drivers' => $shipper->drivers_count, 'percentage' => $truck_required_exist];
        }
        $totalTime = 0;
        $totalCount = 0;
        foreach ($shippersFinished->get() as $shipper) {
            $shipperTotalTime = 0;
            $date = null;
            $count = 0;
            foreach($shipper->loadStatus as $load){
                $shipperTotalTime += Carbon::parse($load->accepted_timestamp)->diffInMinutes($load->finished_timestamp);
                $count++;
            }
            $totalTime += $shipperTotalTime;
            $totalCount += $count;
            if ($shipperAvg[$shipper->id]) {
                $shipperAvg[$shipper->id] += ['loadTime'=>round(($count !== 0) ? $shipperTotalTime/$count : 0)];
            }
        }
        foreach ($dailyLoads as $shipper) {
            if ($shipperAvg[$shipper->id]) {
                $shipperAvg[$shipper->id] += ['total_loads'=> $shipper->loads, 'total_trucks'=> $shipper->trucks];
            }
        }
        return [
            'shipperAvg' => $shipperAvg,
            'LoadTimeAvg' => round(($totalCount !== 0) ? $totalTime/$totalCount : 0),
        ];
    }
}

