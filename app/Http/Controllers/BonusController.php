<?php

namespace App\Http\Controllers;

use App\Models\Bonus;
use App\Models\BonusType;
use App\Models\Carrier;
use App\Traits\EloquentQueryBuilder\GetSimpleSearchData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class BonusController extends Controller
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
            'type' => ['required', 'exists:bonus_types,id'],
            'carriers' => ['nullable', 'array', 'exists:carriers,id'],
            'amount' => ['required', 'numeric'],
            'description' => ['required', 'string', 'max:512'],
            'date' => ['required', 'date'],
        ]);
    }

    /**
     * @return array
     */
    private function createEditParams(): array
    {
        return [
            'types' => [null => ''] + BonusType::pluck('name', 'id')->toArray(),
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
                $bonus = Bonus::whereHas('carriers', function ($q) {
                    $q->whereNull('carrier_payment_id');
                })
                    ->findOrFail($id);
            else
                $bonus = new Bonus();

            $bonus->bonus_type_id = $request->type;
            $bonus->amount = $request->amount;
            $bonus->description = $request->description;
            $bonus->date = $request->date;
            $bonus->save();

            $carriers = $request->carriers ?? Carrier::pluck('id')->toArray();
            $bonus->carriers()->sync($carriers);

            return $bonus;
        });
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('bonuses.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $params = $this->createEditParams();
        return view('bonuses.create', $params);
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
        $request->date = $request->date_submit;
        $this->validator($request->all())->errors();

        $this->storeUpdate($request);

        return redirect()->route('bonus.index');
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
        $bonus = Bonus::findOrFail($id);
        $params = compact('bonus') + $this->createEditParams();
        return view('bonuses.edit', $params);
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
        $request->date = $request->date_submit;
        $this->validator($request->all())->validate();

        $this->storeUpdate($request, $id);

        return redirect()->route('bonus.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id)
    {
        $expense = Bonus::findOrFail($id);

        if ($expense)
            return ['success' => $expense->delete()];
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
            case 'carriers':
            case 'bonus_type':
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
        $query = Bonus::select([
            "bonuses.id",
            "bonuses.bonus_type_id",
            "bonuses.amount",
            "bonuses.description",
            "bonuses.date",
        ])
            ->with([
                'carriers' => function ($q) {
                    $q->select('id','name')
                        ->skip(0)->take(3);
                },
                'bonus_type:id,name',
            ]);

        if ($request->searchable) {
            foreach ($request->searchable as $item) {
                if ($item === 'carriers' && strtolower($request->search) === "all")
                    $query->whereDoesntHave('carriers');
            }
        }

        return $this->multiTabSearchData($query, $request, 'getRelationArray', 'orWhere');
    }
}
