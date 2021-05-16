<?php

namespace App\Http\Controllers;

use App\Models\Rental;
use App\Traits\EloquentQueryBuilder\GetSimpleSearchData;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RentalController extends Controller
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
            'carrier_id' => ['required', 'exists:carriers,id'],
            'driver_id' => ['required', 'exists:drivers,id'],
            'trailer_id' => ['required', 'exists:trailers,id'],
            'date' => ['required', 'date'],
            'period' => ['required', 'digits_between:0,2'],
            'cost' => ['required', 'numeric'],
            'deposit' => ['required', 'numeric'],
        ]);
    }

    /**
     * @return array
     */
    private function createEditParams(): array
    {
        return [
            'periods' => [null => 'Select', 'Weekly', 'Monthly', 'Annual'],
        ];
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('rentals.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $params = $this->createEditParams();
        return view('rentals.create', $params);
    }

    private function storeUpdate(Request $request, $id = null): Rental
    {
        if ($id)
            $rental = Rental::find($id);
        else {
            $rental = new Rental();
            $rental->status = 'uninspected';
        }

        $rental->trailer_id = $request->trailer_id;
        $rental->carrier_id = $request->carrier_id;
        $rental->driver_id = $request->driver_id;
        $rental->date = Carbon::parse($request->date);
        $rental->cost = $request->cost;
        $rental->deposit = $request->deposit;
        $rental->deposit_is_paid = $request->deposit_is_paid ?? null;
        $rental->period = $request->period;
        $rental->save();

        return $rental;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validator($request->all())->validate();

        $this->storeUpdate($request);

        return redirect()->route('rental.index');
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $rental = Rental::find($id);
        $params = compact('rental') + $this->createEditParams();
        return view('rentals.edit', $params);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validator($request->all())->validate();

        $this->storeUpdate($request, $id);

        return redirect()->route('rental.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $rental = Rental::find($id);

        if ($rental) {
            return ['success' => $rental->delete()];
        } else
            return ['success' => false];
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request)
    {
        $query = Rental::select([
            "rentals.id",
            "rentals.date",
            "rentals.carrier_id",
            "rentals.driver_id",
            "rentals.trailer_id",
        ])
            ->with(['carrier:id,name', 'driver:id,name', 'trailer:id,number']);

        if ($request->searchable) {
            $searchable = [];
            foreach ($request->searchable as $item) {
                switch ($item) {
                    case 'carrier':
                    case 'driver':
                        $query->whereHas($item, function ($s) use ($request) {
                            $s->where('name', 'LIKE', "%$request->search%");
                        });
                        break;
                    case 'trailer':
                        $query->whereHas($item, function ($s) use ($request) {
                            $s->where('number', 'LIKE', "%$request->search%");
                        });
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
