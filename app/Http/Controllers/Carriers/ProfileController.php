<?php

namespace App\Http\Controllers\Carriers;

use App\Http\Controllers\Controller;
use App\Models\CarrierEquipment;
use App\Models\CarrierEquipmentType;
use App\Traits\Paperwork\PaperworkFilesFunctions;

class ProfileController extends Controller
{
    use PaperworkFilesFunctions;

    public function profile()
    {
        $carrier = auth()->user();
        $createEdit = $this->getPaperworkByType('carrier');
        $paperworkUploads = $this->getFilesPaperwork($createEdit['filesUploads'], $carrier->id);
        $paperworkTemplates = $this->getTemplatesPaperwork($createEdit['filesTemplates'], $carrier->id);
        $equipmentTypes = [null => ''] + CarrierEquipmentType::pluck('name', 'id')->toArray();
        $equipment = CarrierEquipment::where('carrier_id', auth()->user()->id)->first();
        $params = compact('carrier', 'paperworkUploads', 'paperworkTemplates', 'equipment', 'equipmentTypes') + $createEdit;
        return view('subdomains.carriers.profile.edit', $params);
    }
}
