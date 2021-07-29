<?php

namespace App\Http\Controllers;

use App\Models\Carrier;
use App\Models\Charge;
use App\Traits\EloquentQueryBuilder\GetSimpleSearchData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ChargeController extends Controller
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
            'period' => ['required'],
            'carriers' => ['nullable', 'array', 'exists:carriers,id'],
            'amount' => ['required', 'numeric'],
            'description' => ['required', 'string', 'max:512'],
        ]);
    }

    /**
     * @return array
     */
    private function createEditParams(): array
    {
        return [
            'periods' => [null => '', 'single' => 'Single', 'weekly' => 'Weekly'],
        ];
    }

    /**
     * @param Request $request
     * @param null $id
     * @return mixed
     */
    private function storeUpdate(Request $request, $id = null)
    {
        return DB::transaction(function () use ($request, $id) {
            if ($id)
                $charge = Charge::findOrFail($id);
            else
                $charge = new Charge();

            $charge->amount = $request->amount;
            $charge->description = $request->description;
            $charge->period = $request->period;
            $charge->save();

            $charge->carriers()->sync($request->carriers);

            return $charge;
        });
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('charges.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $params = $this->createEditParams();
        return view('charges.create', $params);
    }

    public function diesel()
    {
        $carriers = Carrier::pluck('name', 'id')->toArray();
        $params = compact('carriers');
        return view('charges.diesel', $params);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $this->validator($request->all())->validate();

        $this->storeUpdate($request);

        return redirect()->route('charge.index');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeDiesel(Request $request)
    {
        foreach ($request->diesel as $carrier_id => $item) {

            if (!$item || !is_numeric($item))
                continue;
            $innerRequest = new Request();
            $innerRequest->setMethod('POST');
            $innerRequest->request->add(['carriers' => [$carrier_id]]);
            $innerRequest->request->add(['amount' => $item]);
            $innerRequest->request->add(['period' => 'single']);
            $innerRequest->request->add(['description' => 'Diesel charge.']);

            //$this->validator($innerRequest->toArray());

            $this->storeUpdate($innerRequest);
        }

        return redirect()->route('charge.index');
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
        $charge = Charge::findOrFail($id);
        $params = compact('charge') + $this->createEditParams();
        return view('charges.edit', $params);
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

        return redirect()->route('charge.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id)
    {
        $expense = Charge::findOrFail($id);

        if ($expense)
            return ['success' => $expense->delete()];
        else
            return ['success' => false];
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request)
    {
        $query = Charge::select([
            "charges.id",
            "charges.amount",
            "charges.period",
        ])
            ->with('carriers:id,name');

        if ($request->searchable) {
            $searchable = [];
            $statement = "whereHas";
            foreach ($request->searchable as $item) {
                switch ($item) {
                    case 'carriers':
                        if (strtolower($request->search) == "all")
                            $query->whereDoesntHave('carriers');
                        else
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
