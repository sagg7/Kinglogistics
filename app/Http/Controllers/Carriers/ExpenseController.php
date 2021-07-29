<?php

namespace App\Http\Controllers\Carriers;

use App\Http\Controllers\Controller;
use App\Models\CarrierExpense;
use App\Traits\EloquentQueryBuilder\GetSimpleSearchData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ExpenseController extends Controller
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
            'type' => ['required'],
            'truck_id' => ['nullable', 'exists:trucks,id'],
            'amount' => ['required', 'numeric'],
            'description' => ['required', 'string', 'max:512'],
            'mileage' => ['nullable', 'numeric'],
            'gallons' => ['nullable', 'numeric'],
        ]);
    }

    /**
     * @return array
     */
    private function createEditParams(): array
    {
        return [
            'types' => [null => '', 'diesel' => 'Diesel', 'salary' => 'Salary', 'repairments' => 'Repairments', 'other' => 'Other'],
        ];
    }

    /**
     * @param Request $request
     * @param null $id
     * @return CarrierExpense
     */
    private function storeUpdate(Request $request, $id = null): CarrierExpense
    {
        if ($id)
            $expense = CarrierExpense::where('carrier_id', auth()->user()->id)
                ->whereNull('carrier_payment_id')
                ->findOrFail($id);
        else {
            $expense = new CarrierExpense();
            $expense->carrier_id = auth()->user()->id;
        }

        $expense->type = $request->type;
        $expense->amount = $request->amount;
        $expense->truck_id = $request->truck_id;
        $expense->description = $request->description;
        $expense->mileage = $request->mileage;
        $expense->gallons = $request->gallons;
        $expense->save();

        return $expense;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('subdomains.carriers.expenses.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $params = $this->createEditParams();
        return view('subdomains.carriers.expenses.create', $params);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validator($request->all())->errors();

        $this->storeUpdate($request);

        return redirect()->route('expense.index');
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
        $expense = CarrierExpense::with('truck')
            ->where('carrier_id', auth()->user()->id)
            ->findOrFail($id);
        $params = compact('expense') + $this->createEditParams();
        return view('subdomains.carriers.expenses.edit', $params);
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

        return redirect()->route('expense.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id)
    {
        $expense = CarrierExpense::where('carrier_id', auth()->user()->id)
            ->whereNull('carrier_payment_id')
            ->findOrFail($id);

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
        $query = CarrierExpense::select([
            "carrier_expenses.id",
            "carrier_expenses.type",
            "carrier_expenses.amount",
            "carrier_expenses.created_at",
        ])
            ->where('carrier_id', auth()->user()->id);

        return $this->simpleSearchData($query, $request, 'orWhere');
    }
}
