<?php

namespace App\Http\Controllers\Carriers;

use App\Http\Controllers\Controller;
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
        $params = compact('carrier', 'paperworkUploads', 'paperworkTemplates') + $createEdit;
        return view('subdomains.carriers.profile.edit', $params);
    }
}
