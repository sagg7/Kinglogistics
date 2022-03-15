<?php

namespace App\Http\Controllers;
use App\Exports\TemplateExport;
use App\Models\Income;
use App\Models\IncomeAccount;
use App\Models\IncomeType;
use App\Traits\EloquentQueryBuilder\GetSimpleSearchData;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use App\Exports\IncomeErrorsExport;
use App\Imports\IncomeImport;
use App\Jobs\ProcessDeleteFileDelayed;
use Maatwebsite\Excel\Facades\Excel;

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
            'account' => ['required', 'exists:income_accounts,id'],
            'amount' => ['required', 'numeric'],
            'description' => ['required', 'string', 'max:512'],
            'note' => ['string', 'max:512'],
        ]);
    }

    /**
     * @return array
     */
    private function createEditParams(): array
    {
        return [
            'types' => [null => ''] + IncomeType::whereHas('broker', function ($q) {
                    $q->where('id', session('broker'));
                })
                    ->pluck('name', 'id')->toArray(),
            'accounts' => [null => ''] + IncomeAccount::whereHas('broker', function ($q) {
                    $q->where('id', session('broker'));
                })
                    ->pluck('name', 'id')->toArray(),
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
            $income = Income::whereHas('broker', function ($q) {
                $q->where('id', session('broker'));
            })
                ->findOrFail($id);
        else {
            $income = new Income();
            $income->broker_id = session('broker');
        }

        $income->type_id = $request->type;
        $income->account_id = $request->account;
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
        $income = Income::whereHas('broker', function ($q) {
            $q->where('id', session('broker'));
        })
            ->findOrFail($id);
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
     * @return array
     */
    public function destroy(int $id)
    {
        $income = Income::whereHas('broker', function ($q) {
            $q->where('id', session('broker'));
        })
            ->findOrFail($id);

        return ['success' => $income->delete()];
    }

    /**
     * @param $item
     * @return array|string[]|null
     */
    private function getRelationArray($item): ?array
    {
        switch ($item) {
            case 'type':
            case 'account':
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
            "incomes.account_id",
            "incomes.amount",
            "incomes.date",

        ])
            ->whereHas('broker', function ($q) {
                $q->where('id', session('broker'));
            })
            ->with([
                'type:id,name',
                'account:id,name',
            ]);

        return $this->multiTabSearchData($query, $request, 'getRelationArray');
    }

    public function downloadXLS(Request $request)
    {
        $income = Income::with([
            'type:id,name',
            'account:id,name',
        ])
            ->whereHas('broker', function ($q) {
                $q->where('id', session('broker'));
            })
            ->get();

        if (count($income) === 0)
            return redirect()->back()->withErrors('There are no income to generate the document');
        $data = [];
        foreach ($income as $incomes) {
            $data[] = [
                        'date' => $incomes->date ? Carbon::createFromFormat('Y-m-d H:i:s', $incomes->date)->format('m/d/Y H:i') : null,
                        'type' => $incomes->type->name,
                        'account' => $incomes->account ? $incomes->account->name : null,
                        'amount' => $incomes->amount,
                        'description' => $incomes->description ? $incomes->description: null,
                        'note'=> $incomes->note ? $incomes->note: null
            ];
        }

        return (new TemplateExport([
            "data" => $data,
            "headers" => ["Date", "Type", "Account", "Amount", "Description", "Note"],
            "formats" => [
                'D' => NumberFormat::FORMAT_CURRENCY_USD_SIMPLE,
            ],
        ]))->download("Income" . " - " . Carbon::now()->format('m-d-Y') . ".xlsx");
    }

    
    public function uploadIncomeExcel(Request $request)
    {
        $import = new IncomeImport;
        Excel::import($import, $request->fileExcel);
        $data = $import->data;

        $result = ['success' => true];

        if ($data['errors']) {
            $directory = "temp/xls/" . md5(Carbon::now());
            $path = $directory . "/Income Excel Errors.xlsx";
            $publicPath = "public/" . $path;
            (new IncomeErrorsExport($data['errors']))->store($publicPath);
            ProcessDeleteFileDelayed::dispatch($directory, true)->delay(now()->addMinutes(1));
            $result['errors_file'] = asset($path);
        }

        return $result;
    }
}
