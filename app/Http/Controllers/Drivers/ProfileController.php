<?php

namespace App\Http\Controllers\Drivers;

use App\Http\Controllers\Controller;
use App\Models\Driver;
use App\Models\Shipper;
use App\Traits\Paperwork\PaperworkFilesFunctions;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    use PaperworkFilesFunctions;

    /**
     * @return array
     */
    private function createEditParams(): array
    {
        return [
                'shippers' => Shipper::skip(0)->take(15)->pluck('name', 'id'),
            ] + $this->getPaperworkByType('driver');
    }

    public function profile()
    {
        $driver = auth()->user();
        $driver->shippers = $driver->shippers()->pluck('id')->toArray();
        $createEdit = $this->createEditParams();
        $paperworkUploads = $this->getFilesPaperwork($createEdit['filesUploads'], $driver->id);
        $paperworkTemplates = $this->getTemplatesPaperwork($createEdit['filesTemplates'], $driver->id);
        $params = compact('driver', 'paperworkUploads', 'paperworkTemplates') + $createEdit;
        return view('subdomains.drivers.profile.edit', $params);
    }
}
