<?php

namespace App\Http\Controllers\Carriers;

use App\Enums\LoadTypeEnum;
use App\Http\Controllers\Controller;
use App\Models\Load;
use App\Models\LoadTrailerType;
use App\Models\RoadLoad;
use App\Traits\EloquentQueryBuilder\GetSimpleSearchData;
use Carbon\Carbon;
use Illuminate\Http\Request;

class RoadLoadController extends Controller
{
    use GetSimpleSearchData;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $params = [
            'trailer_types' => LoadTrailerType::pluck('name', 'id')->toArray(),
            'radius' => [25 => 25, 50 => 50, 100 => 100, 150 => 150, 200 => 200, 300 => 300],
            'load_sizes' => [null => 'All', 'full' => 'FULL (Full loads)', 'partial' =>  'LTL (Partial loads)'],
            'weight' => [null => 'All', 40000 => 'Less than: 40,000', 30000 => 'Less than: 30,000', 20000 => 'Less than: 20,000', 10000 => 'Less than: 10,000'],
            'length' => [null => 'All', 40 => 'Less than: 40', 30 => 'Less than: 30', 20 => 'Less than: 20', 10 => 'Less than: 10'],
        ];

        return view('subdomains.carriers.loads.road.index', $params);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\RoadLoad  $roadLoad
     * @return \Illuminate\Http\Response
     */
    public function show(RoadLoad $roadLoad)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\RoadLoad  $roadLoad
     * @return \Illuminate\Http\Response
     */
    public function edit(RoadLoad $roadLoad)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\RoadLoad  $roadLoad
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, RoadLoad $roadLoad)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\RoadLoad  $roadLoad
     * @return \Illuminate\Http\Response
     */
    public function destroy(RoadLoad $roadLoad)
    {
        //
    }
    /**
     * @param Request $request
     * @return array
     */
    public function search(Request $request)
    {
        $query = Load::select([
            'id',
            'date',
            'shipper_id',
            'mileage',
            'weight',
        ])
            ->where('type', LoadTypeEnum::ROAD)
            ->whereDate('date', '>=', str_replace("/","-",$request->ship_date_start))
                ->whereDate('date', '<=', str_replace("/","-",$request->ship_date_end))
            ->where(function ($q) use ($request) {
                if ($request->weight) {
                    $q->where('weight', '<=', $request->weight);
                }
            })
            ->whereHas('road', function ($q) use ($request) {
                if ($request->load_size) {
                    $q->where('load_size', $request->load_size);
                }
                if ($request->length) {
                    $q->where('length', '<=', $request->length);
                }
                if ($request->trailer_type) {
                    $q->whereIn('trailer_type_id', $request->trailer_type);
                }
            })
            ->with([
                'road' => function ($q) {
                    $q->select([
                        'id',
                        'load_id',
                        'trailer_type_id',
                        'mode_id',
                        'deadhead_miles',
                        'origin_city_id',
                        'destination_city_id',
                        'load_size',
                        'length',
                        'pay_rate',
                        'created_at',
                    ])
                    ->with([
                        'mode:id,name',
                        'trailer_type:id,name',
                        'origin_city' => function ($q) {
                            $q->select(['id', 'name', 'state_id'])
                                ->with('state:id,abbreviation');
                        },
                        'destination_city' => function ($q) {
                            $q->select(['id', 'name', 'state_id'])
                                ->with('state:id,abbreviation');
                        },
                    ]);
                },
                'shipper:id,name,factoring',
            ])
            ->get();

        $now = Carbon::now();
        foreach ($query as $load) {
            $load->age = Carbon::parse($load->road->created_at)->diff($now);
            $start = $load->road->created_at;
            $diffInSeconds = $start->diffInSeconds($now);
            if ($diffInSeconds <= 86400) { // Checks if it's older than 24 hrs
                $load->age = $start->diff($now)->format('%hh%im');
            } else {
                $load->age = $start->diffInHours($now) . 'd' . $start->diff($now)->format('%hh%im');
            }
            $load->rate_mile = ($load->road->pay_rate && $load->mileage) ? ($load->road->pay_rate / $load->mileage) : 0;
        }

        return $query;
    }
}
