<?php

namespace App\Http\Controllers\Carriers;

use App\Http\Controllers\Controller;
use App\Models\CarrierEquipment;
use App\Models\CarrierEquipmentType;
use App\Traits\EloquentQueryBuilder\GetSimpleSearchData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CarrierEquipmentController extends Controller
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
            'equipment_type' => ['required', 'exists:carrier_equipment_types,id'],
            'name' => ['required', 'string', 'max:255'],
            'status' => ['required'],
            'description' => ['required', 'string', 'max:512'],
        ],
            [],
            [
                'trailer_type_id' => 'trailer type',
                'chassis_type_id' => 'chassis type',
            ]);
    }

    /**
     * @param Request $request
     * @return CarrierEquipment
     * @throws \Illuminate\Validation\ValidationException
     */
    public function storeUpdate(Request $request, int $id = null): CarrierEquipment
    {
        $this->validator($request->all())->validate();

        if ($id)
            $equipment = CarrierEquipment::where('carrier_id', auth()->user()->id)->findOrFail($id);
        else {
            $equipment = new CarrierEquipment();
            $equipment->carrier_id = auth()->user()->id;
        }
        $equipment->carrier_equipment_type_id = $request->equipment_type;
        $equipment->name = $request->name;
        $equipment->status = $request->status;
        $equipment->description = $request->description;
        $equipment->save();

        return $equipment;
    }

    /**
     * @return array
     */
    private function createEditParams(): array
    {
        return [
            'equipmentTypes' => [null => ''] + CarrierEquipmentType::pluck('name', 'id')->toArray(),
        ];
    }

    public function create()
    {
        $params = $this->createEditParams();
        return view('subdomains.carriers.profile.equipment.create', $params);
    }

    public function store(Request $request)
    {
        $this->storeUpdate($request);

        return redirect()->route('carrier.profile');
    }

    public function edit(int $id)
    {
        $equipment = CarrierEquipment::where('carrier_id', auth()->user()->id)->findOrFail($id);
        $params = compact('equipment') + $this->createEditParams();
        return view('subdomains.carriers.profile.equipment.edit', $params);
    }

    public function update(Request $request, int $id)
    {
        $this->storeUpdate($request, $id);

        return redirect()->route('carrier.profile');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return array|false[]
     */
    public function destroy(int $id): array
    {
        $equipment = CarrierEquipment::where('carrier_id', auth()->user()->id)->findOrFail($id);

        if ($equipment)
            return ['success' => $equipment->delete()];
        else
            return ['success' => false];
    }

    public function search(Request $request)
    {
        $query = CarrierEquipment::select([
            "carrier_equipment.id",
            "carrier_equipment.name",
            "carrier_equipment.status",
            //"carrier_equipment.description",
        ]);

        return $this->multiTabSearchData($query, $request);
    }
}
