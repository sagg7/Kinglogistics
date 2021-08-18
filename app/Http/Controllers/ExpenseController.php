<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\ExpenseType;
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
            'type' => ['required', 'exists:expense_types,id'],
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
            'types' => [null => ''] + ExpenseType::pluck('name', 'id')->toArray(),
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
            $expense = Expense::findOrFail($id);
        else
            $expense = new Expense();

        $expense->type_id = $request->type;
        $expense->amount = $request->amount;
        $expense->description = $request->description;
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
        return view('expenses.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $params = $this->createEditParams();
        return view('expenses.create', $params);
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
     * @param  \App\Models\Expense  $expense
     * @return \Illuminate\Http\Response
     */
    public function show(Expense $expense)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Expense  $expense
     * @return \Illuminate\Http\Response
     */
    public function edit(int $id)
    {
        $expense = Expense::findOrFail($id);
        $params = compact('expense') + $this->createEditParams();
        return view('expenses.edit', $params);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Expense  $expense
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ind $id)
    {
        $this->validator($request->all())->errors();

        $this->storeUpdate($request, $id);

        return redirect()->route('expense.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Expense  $expense
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id)
    {
        $expense = Expense::findOrFail($id);

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
            "expenses.type_id",
            "expenses.amount",
            "expenses.created_at",
        ])
            ->with('type:id,name');

        if ($request->searchable) {
            $searchable = [];
            $statement = "whereHas";
            foreach ($request->searchable as $item) {
                switch ($item) {
                    case 'type':
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
