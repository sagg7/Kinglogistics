<?php

namespace App\Http\Controllers;

use App\Models\Income;
use App\Models\IncomeType;
use App\Traits\EloquentQueryBuilder\GetSimpleSearchData;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class IncomeController extends Controller
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
            'type' => ['required', 'exists:income_types,id'],
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
            'types' => [null => ''] + IncomeType::pluck('name', 'id')->toArray(),
        ];
    }

    /**
     * @param Request $request
     * @param null $id
     * @return Income
     */
    private function storeUpdate(Request $request, $id = null): Income
    {
        if ($id)
            $income = Income::findOrFail($id);
        else
            $income = new Income();

        $income->type_id = $request->type;
        $income->amount = $request->amount;
        $income->description = $request->description;
        $income->date = Carbon::parse($request->date_submit);
        $income->note = $request->note;
        $income->user_id = auth()->user()->id;
        $income->save();

        return $income;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('incomes.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $params = $this->createEditParams();
        return view('incomes.create', $params);
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

        return redirect()->route('income.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Income  $income
     * @return \Illuminate\Http\Response
     */
    public function show(Income $income)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Income  $income
     * @return \Illuminate\Http\Response
     */
    public function edit(int $id)
    {
        $income = Income::findOrFail($id);
        $params = compact('income') + $this->createEditParams();
        return view('incomes.edit', $params);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Income  $income
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validator($request->all())->errors();

        $this->storeUpdate($request, $id);

        return redirect()->route('income.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Income  $income
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id)
    {
        $income = Income::findOrFail($id);

        if ($income)
            return ['success' => $income->delete()];
        else
            return ['success' => false];
    }

    /**
     * @param $item
     * @return array|string[]|null
     */
    private function getRelationArray($item): ?array
    {
        switch ($item) {
            case 'type':
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
        $query = Income::select([
            "incomes.id",
            "incomes.type_id",
            "incomes.amount",
            "date",

        ])
            ->with('type:id,name');

        return $this->multiTabSearchData($query, $request, 'getRelationArray');
    }
}
