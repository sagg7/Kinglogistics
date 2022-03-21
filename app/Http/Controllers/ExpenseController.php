<?php

namespace App\Http\Controllers;
use App\Exports\TemplateExport;
use App\Models\Expense;
use App\Models\ExpenseAccount;
use App\Models\ExpenseType;
use App\Traits\EloquentQueryBuilder\GetSimpleSearchData;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Facades\Excel;
use App\Jobs\ProcessDeleteFileDelayed;
use App\Exports\ExpenseErrorsExport;
use App\Imports\ExpenseImport;


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
            'account' => ['required', 'exists:expense_accounts,id'],
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
            'types' => [null => ''] + ExpenseType::whereHas('broker', function ($q) {
                    $q->where('id', session('broker'));
                })
                    ->pluck('name', 'id')->toArray(),
            'accounts' => [null => ''] + ExpenseAccount::whereHas('broker', function ($q) {
                    $q->where('id', session('broker'));
                })
                    ->pluck('name', 'id')->toArray(),
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
            $expense = Expense::whereHas('broker', function ($q) {
                $q->where('id', session('broker'));
            })
                ->findOrFail($id);
        else {
            $expense = new Expense();
            $expense->broker_id = session('broker');
        }

        $expense->type_id = $request->type;
        $expense->account_id = $request->account;
        $expense->amount = $request->amount;
        $expense->description = $request->description;
        $expense->date = Carbon::parse($request->date_submit);
        $expense->note = $request->note;
        $expense->user_id = auth()->user()->id;
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
        $expense = Expense::whereHas('broker', function ($q) {
            $q->where('id', session('broker'));
        })
            ->findOrFail($id);
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
    public function update(Request $request, $id)
    {
        $this->validator($request->all())->errors();

        $this->storeUpdate($request, $id);

        return redirect()->route('expense.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Expense  $expense
     * @return array
     */
    public function destroy(int $id)
    {
        $expense = Expense::whereHas('broker', function ($q) {
            $q->where('id', session('broker'));
        })
            ->findOrFail($id);

        return ['success' => $expense->delete()];
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
        $query = Expense::select([
            "expenses.id",
            "expenses.type_id",
            "expenses.account_id",
            "expenses.amount",
            "expenses.date",

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
        $expense = Expense::with([
            'type:id,name',
            'account:id,name',
        ])
            ->whereHas('broker', function ($q) {
                $q->where('id', session('broker'));
            })
            ->get();

        if (count($expense) === 0)
            return redirect()->back()->withErrors('There are no rentals to generate the document');
        $data = [];
        foreach ($expense as $expenses) {
            $data[] = [
                         'date' => $expenses->date ? Carbon::createFromFormat('Y-m-d H:i:s', $expenses->date)->format('m/d/Y H:i') : null,
                        'type' => $expenses->type->name,
                        'account' => $expenses->account->name ? $expenses->account->name : null,
                        'amount' => $expenses->amount,
                        'description' => $expenses->description ? $expenses->description: null,
                        'note'=> $expenses->note ? $expenses->note: null
            ];
        }

        return (new TemplateExport([
            "data" => $data,
            "headers" => ["Date", "Type", "Account", "Amount", "Description", "Note"],
            "formats" => [
                'D' => NumberFormat::FORMAT_CURRENCY_USD_SIMPLE,
            ],
        ]))->download("Expense " . " - " . Carbon::now()->format('m-d-Y') . ".xlsx");
    }

    public function downloadTmpXLS()
    {
        $data = [];
        return (new TemplateExport([
            "data" => $data,
            "headers" => ["Date", "Type", "Account", "Amount", "Description", "Note"],
            "formats" => [
                'D' => NumberFormat::FORMAT_CURRENCY_USD_SIMPLE,
            ],
        ]))->download("Expense" . " - " . Carbon::now()->format('m-d-Y') . ".xlsx");
    }

    public function uploadExpenseExcel(Request $request)
    {
        $import = new ExpenseImport;
        Excel::import($import, $request->fileExcel);
        $data = $import->data;

        $result = ['success' => true];

        if ($data['errors']) {
            $directory = "temp/xls/" . md5(Carbon::now());
            $path = $directory . "/Expense_Excel_Errors.xlsx";
            $publicPath = "public/" . $path;
            (new ExpenseErrorsExport($data['errors']))->store($publicPath);
            ProcessDeleteFileDelayed::dispatch($directory, true)->delay(now()->addMinutes(1));
            $result['errors_file'] = asset("storage/" . $path);
        }

        return $result;
    }
}
