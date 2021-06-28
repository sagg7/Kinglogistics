<?php

namespace App\Http\Controllers\Carriers;

use App\Http\Controllers\Controller;
use App\Models\Expense;
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
            'truck_id' => ['required', 'exists:trucks,id'],
            'amount' => ['required', 'numeric'],
            'description' => ['nullable', 'string', 'max:512'],
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
     * @return Expense
     */
    private function storeUpdate(Request $request, $id = null): Expense
    {
        if ($id)
            $expense = Expense::where('carrier_id', auth()->user()->id)
                ->whereNull('carrier_payment_id')
                ->findOrFail($id);
        else {
            $expense = new Expense();
            $expense->carrier_id = auth()->user()->id;
        }

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
        $expense = Expense::where('carrier_id', auth()->user()->id)
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
        $expense = Expense::where('carrier_id', auth()->user()->id)
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
        $query = Expense::select([
            "expenses.id",
            "expenses.type",
            "expenses.amount",
            "expenses.created_at",
        ]);

        return $this->simpleSearchData($query, $request, 'orWhere');
    }
}
