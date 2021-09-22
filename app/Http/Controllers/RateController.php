<?php

namespace App\Http\Controllers;

use App\Models\Rate;
use App\Models\RateGroup;
use App\Traits\EloquentQueryBuilder\GetSelectionData;
use App\Traits\EloquentQueryBuilder\GetSimpleSearchData;
use App\Traits\Load\RecalculateTotals;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class RateController extends Controller
{
    use GetSimpleSearchData, GetSelectionData, RecalculateTotals;

    /**
     * @param array $data
     * @param int|null $id
     * @return \Illuminate\Contracts\Validation\Validator
     */
    private function validator(array $data, int $id = null)
    {
        return Validator::make($data, [
            'rate_group' => ['required', 'exists:rate_groups,id'],
            'shipper' => ['required', 'exists:carriers,id'],
            'zone' => ['required', 'exists:zones,id'],
            'start_mileage' => ['required', 'numeric'],
            'end_mileage' => ['required', 'numeric'],
            'shipper_rate' => ['required', 'numeric'],
            'carrier_rate' => ['required', 'numeric'],
        ]);
    }

    /**
     * @return array
     */
    private function createEditParams(): array
    {
        return [
            'rate_groups' => [null => ''] + RateGroup::pluck('name', 'id')->toArray(),
        ];
    }

    /**
     * @param Request $request
     * @param null $id
     * @return Rate
     */
    private function storeUpdate(Request $request, $id = null): Rate
    {
        return DB::transaction(function () use ($request, $id) {
            $ratesValuesChanged = false;
            if ($id) {
                $rate = Rate::findOrFail($id);
                if (($rate->shipper_rate != $request->shipper_rate) ||
                    ($rate->carrier_rate != $request->carrier_rate)) {
                    $ratesValuesChanged = true;
                }
            } else
                $rate = new Rate();

            $rate->rate_group_id = $request->rate_group;
            $rate->shipper_id = $request->shipper;
            $rate->zone_id = $request->zone;
            $rate->start_mileage = $request->start_mileage;
            $rate->end_mileage = $request->end_mileage;
            $rate->shipper_rate = $request->shipper_rate;
            $rate->carrier_rate = $request->carrier_rate;
            $rate->save();

            if ($ratesValuesChanged) {
                $rate->load(['trips']);
                foreach ($rate->trips as $trip) {
                    $this->byRateChange($trip, $rate->id);
                }
            }

            return $rate;
        });
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('rates.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $params = $this->createEditParams();
        return view('rates.create', $params);
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

        return redirect()->route('rate.index');
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
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
        $rate = Rate::findOrFail($id);
        $params = compact('rate') + $this->createEditParams();
        return view('rates.edit', $params);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, int $id)
    {
        $this->validator($request->all())->validate();

        $this->storeUpdate($request, $id);

        return redirect()->route('rate.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id)
    {
        $rate = Rate::findOrFail($id);

        if ($rate)
            return ['success' => $rate->delete()];
        else
            return ['success' => false];
    }

    public function selection(Request $request)
    {
        $query = Rate::select([
            'rates.id',
            DB::raw("CONCAT(rate_groups.name, ': ', rates.start_mileage, ' - ', rates.end_mileage, ' miles') as text"),
        ])
            ->join('rate_groups', 'rate_groups.id', '=', 'rates.rate_group_id')
            ->where('shipper_id', $request->shipper)
            ->where('zone_id', $request->zone)
            ->where("name", "LIKE", "%$request->search%");

        return $this->selectionData($query, $request->take, $request->page);
    }

    /**
     * @param $item
     * @return array|string[]|null
     */
    private function getRelationArray($item): ?array
    {
        switch ($item) {
            case 'rate_group':
            case 'shipper':
            case 'zone':
                $array = [
                    'relation' => $item,
                    'column' => 'name',
                ];
                break;
            default:
                $array = null;
                break;
        }

        return $array;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request)
    {
        $query = Rate::select([
            "rates.id",
            "rates.rate_group_id",
            "rates.shipper_id",
            "rates.zone_id",
            "rates.start_mileage",
            "rates.end_mileage",
            "rates.shipper_rate",
            "rates.carrier_rate",
        ])
            ->with([
                'rate_group:id,name',
                'shipper:id,name',
                'zone:id,name',
            ]);

        return $this->multiTabSearchData($query, $request, 'getRelationArray');
    }
}
