<?php

namespace App\Http\Controllers;

use App\Models\Rate;
use App\Models\RateGroup;
use App\Traits\EloquentQueryBuilder\GetSimpleSearchData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RateController extends Controller
{
    use GetSimpleSearchData;

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
        if ($id)
            $rate = Rate::findOrFail($id);
        else
            $rate = new Rate();

        $rate->rate_group_id = $request->rate_group;
        $rate->shipper_id = $request->shipper;
        $rate->zone_id = $request->zone;
        $rate->start_mileage = $request->start_mileage;
        $rate->end_mileage = $request->end_mileage;
        $rate->shipper_rate = $request->shipper_rate;
        $rate->carrier_rate = $request->carrier_rate;
        $rate->save();

        return $rate;
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

        if ($request->searchable) {
            $searchable = [];
            $statement = "whereHas";
            foreach ($request->searchable as $item) {
                switch ($item) {
                    case 'rate_group':
                    case 'shipper':
                    case 'zone':
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
