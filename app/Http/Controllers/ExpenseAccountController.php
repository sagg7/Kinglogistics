<?php

namespace App\Http\Controllers;

use App\Models\ExpenseAccount;
use App\Traits\CRUD\crudMessage;
use App\Traits\EloquentQueryBuilder\GetSelectionData;
use App\Traits\EloquentQueryBuilder\GetSimpleSearchData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ExpenseAccountController extends Controller
{
    use GetSelectionData, GetSimpleSearchData, crudMessage;

    /**
     * @param array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    private function validator(array $data): \Illuminate\Contracts\Validation\Validator
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
        ]);
    }

    /**
     * @param Request $request
     * @param null $id
     * @return ExpenseAccount
     */
    private function storeUpdate(Request $request, $id = null): ExpenseAccount
    {
        if ($id) {
            $type = ExpenseAccount::whereHas('broker', function ($q) {
                $q->where('id', session('broker'));
            })
                ->findOrFail($id);
        } else {
            $type = new ExpenseAccount();
            $type->broker_id = session('broker');
        }

        $type->name = $request->name;
        $type->save();

        return $type;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): array
    {
        $this->validator($request->all())->validate();

        $type = $this->storeUpdate($request);

        return ['success' => true, 'data' => $type];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param int|null $id
     * @return array
     */
    public function destroy(Request $request, int $id = null): array
    {
        if (!$id) {
            $id = $request->id;
        }
        $type = ExpenseAccount::whereHas('broker', function ($q) {
            $q->where('id', session('broker'));
        })
            ->findOrFail($id);

        return ['success' => $type->delete()];
    }

    /**
     * @param Request $request
     * @return array
     */
    public function selection(Request $request): array
    {
        $query = ExpenseAccount::select([
            'id',
            'name as text',
        ])
            ->whereHas('broker', function ($q) {
                $q->where('id', session('broker'));
            })
            ->where("name", "LIKE", "%$request->search%");

        return $this->selectionData($query, $request->take, $request->page);
    }
}
