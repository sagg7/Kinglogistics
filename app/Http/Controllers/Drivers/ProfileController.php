<?php

namespace App\Http\Controllers\Drivers;

use App\Http\Controllers\Controller;
use App\Traits\Paperwork\PaperworkFilesFunctions;

class ProfileController extends Controller
{
    use PaperworkFilesFunctions;

    public function profile()
    {
        $driver = auth()->user();
        $createEdit = $this->getPaperworkByType('driver', $driver->id);
        $paperworkUploads = $this->getFilesPaperwork($createEdit['filesUploads'], $driver->id);
        $paperworkTemplates = $this->getTemplatesPaperwork($createEdit['filesTemplates'], $driver->id);
        $params = compact('driver', 'paperworkUploads', 'paperworkTemplates') + $createEdit;
        return view('subdomains.drivers.profile.edit', $params);
    }
}
