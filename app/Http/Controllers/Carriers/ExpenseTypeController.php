<?php

namespace App\Http\Controllers\Carriers;

use App\Http\Controllers\Controller;
use App\Models\CarrierExpenseType;
use App\Traits\CRUD\crudMessage;
use App\Traits\EloquentQueryBuilder\GetSelectionData;
use App\Traits\EloquentQueryBuilder\GetSimpleSearchData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ExpenseTypeController extends Controller
{
    use GetSelectionData, GetSimpleSearchData, crudMessage;

    /**
     * @param array $data
     * @param int|null $id
     * @return \Illuminate\Contracts\Validation\Validator
     */
    private function validator(array $data, int $id = null)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
        ]);
    }

    /**
     * @param Request $request
     * @param null $id
     * @return CarrierExpenseType
     */
    private function storeUpdate(Request $request, $id = null): CarrierExpenseType
    {
        if ($id)
            $type = CarrierExpenseType::where('carrier_id', auth()->user()->id)
                ->findOrFail($id);
        else {
            $type = new CarrierExpenseType();
            $type->carrier_id = auth()->user()->id;
        }

        $type->name = $request->name;
        $type->save();

        return $type;
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

        $type = $this->storeUpdate($request);

        if ($request->ajax())
            return ['success' => true, 'data' => $type];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int|null $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, int $id = null)
    {
        if (!$id)
            $id = $request->id;
        $type = CarrierExpenseType::where('carrier_id', auth()->user()->id)
            ->findOrFail($id);

        if ($type) {
            $message = '';
            if ($type->expenses()->first())
                $message .= "???" . $this->generateCrudMessage(4, 'Expense Type', ['constraint' => 'expenses']) . "<br>";
            if ($message)
                return ['success' => false, 'msg' => $message];
            else
                return ['success' => $type->delete()];
        } else
            return ['success' => false];
    }

    /**
     * @param Request $request
     * @return array
     */
    public function selection(Request $request): array
    {
        $query = CarrierExpenseType::where('carrier_id', auth()->user()->id)
            ->select([
                'id',
                'name as text',
            ])
            ->where("name", "LIKE", "%$request->search%");

        return $this->selectionData($query, $request->take, $request->page);
    }
}
