<?php

namespace App\Http\Controllers;

use App\Models\RateGroup;
use App\Traits\CRUD\crudMessage;
use App\Traits\EloquentQueryBuilder\GetSelectionData;
use App\Traits\EloquentQueryBuilder\GetSimpleSearchData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RateGroupController extends Controller
{
    use GetSelectionData, GetSimpleSearchData, crudMessage;

    /**
     * @param array $data
     * @param int|null $id
     * @return \Illuminate\Contracts\Validation\Validator
     */
    private function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
        ]);
    }

    /**
     * @param Request $request
     * @param null $id
     * @return RateGroup
     */
    private function storeUpdate(Request $request, $id = null): RateGroup
    {
        if ($id)
            $rateGroup = RateGroup::findOrFail($id);
        else
            $rateGroup = new RateGroup();

        $rateGroup->name = $request->name;
        $rateGroup->save();

        return $rateGroup;
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

        $rateGroup = $this->storeUpdate($request);

        if ($request->ajax())
            return ['success' => true, 'data' => $rateGroup];
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validator($request->all())->validate();

        $rateGroup = $this->storeUpdate($request, $id);

        if ($request->ajax())
            return ['success' => true, 'data' => $rateGroup];
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
        $rateGroup = RateGroup::findOrFail($id);

        if ($rateGroup) {
            $message = '';
            if ($rateGroup->rates()->first())
                $message .= "•" . $this->generateCrudMessage(4, 'Rate Group', ['constraint' => 'rates']) . "<br>";
            if ($message)
                return ['success' => false, 'msg' => $message];
            else
                return ['success' => $rateGroup->delete()];
        } else
            return ['success' => false];
    }

    /**
     * @param Request $request
     * @return array
     */
    public function selection(Request $request): array
    {
        $query = RateGroup::select([
            'id',
            'name as text',
        ])
            ->where("name", "LIKE", "%$request->search%");

        return $this->selectionData($query, $request->take, $request->page);
    }
}
