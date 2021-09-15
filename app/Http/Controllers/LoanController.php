<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Traits\EloquentQueryBuilder\GetSimpleSearchData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LoanController extends Controller
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
            'carrier' => ['required', 'exists:carriers,id'],
            'amount' => ['required', 'numeric'],
            'fee_percentage' => ['required', 'numeric'],
            'installments' => ['required', 'numeric'],
            'description' => ['required', 'string', 'max:512'],
        ]);
    }

    /**
     * @param Request $request
     * @param null $id
     * @return Loan
     */
    private function storeUpdate(Request $request, $id = null): Loan
    {
        if ($id)
            $loan = Loan::findOrFail($id);
        else
            $loan = new Loan();

        $loan->carrier_id = $request->carrier;
        $loan->amount = $request->amount;
        $loan->fee_percentage = $request->fee_percentage;
        $loan->installments = $request->installments;
        $loan->description = $request->description;
        $loan->save();

        return $loan;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('loans.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('loans.create');
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

        return redirect()->route('loan.index');
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
        $loan = Loan::findOrFail($id);
        $params = compact('loan');
        return view('loans.create', $params);
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

        return redirect()->route('loan.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id)
    {
        $loan = Loan::findOrFail($id);

        if ($loan)
            return ['success' => $loan->delete()];
        else
            return ['success' => false];
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request)
    {
        $query = Loan::select([
            "loans.id",
            "loans.amount",
            "loans.paid_amount",
            "loans.installments",
            "loans.paid_installments",
            "loans.fee_percentage",
            "loans.carrier_id",
        ])
            ->with('carrier:id,name');

        $relationships = [];
        if ($request->searchable) {
            $searchable = [];
            foreach ($request->searchable as $item) {
                switch ($item) {
                    case 'carrier':
                        $relationships[] = [
                            'relation' => $item,
                            'column' => 'name',
                        ];
                        break;
                    default:
                        $searchable[count($searchable) + 1] = $item;
                        break;
                }
            }
            $request->searchable = $searchable;
        }

        return $this->multiTabSearchData($query, $request, $relationships);
    }
}
