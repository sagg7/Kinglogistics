<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Driver;
use App\Models\Paperwork;
use App\Traits\Paperwork\PaperworkFilesFunctions;
use App\Traits\Storage\S3Functions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaperworkController extends Controller
{
    use PaperworkFilesFunctions, S3Functions;

    public function getPaperwork()
    {
        $driver = auth()->user();
        $paperworkType = $this->getPaperworkByType('driver', $driver->id);
        $paperworkUploads = $this->getFilesPaperwork($paperworkType['filesUploads'], $driver->id);
        $paperworkTemplates = $this->getTemplatesPaperwork($paperworkType['filesTemplates'], $driver->id);

        foreach ($paperworkType['filesUploads'] as $item) {
            if (isset($paperworkUploads[$item->id])) {
                $paperworkUploads[$item->id]['name'] = $item->name;
            }
        }
        $paperworkUploads = array_values($paperworkUploads);

        foreach ($paperworkType['filesTemplates'] as $item) {
            if (isset($paperworkTemplates[$item->id])) {
                $paperworkTemplates[$item->id]['name'] = $item->name;
            }
        }
        $paperworkTemplates = array_values($paperworkTemplates);

        return compact('paperworkUploads', 'paperworkTemplates');
    }

    public function getPaperworkUpload(Request $request)
    {
        $driver = Driver::where(DB::raw('crc32(concat(COALESCE(drivers.id, ""),COALESCE(drivers.email, ""),COALESCE(drivers.password, "")))'), $request->token)
            ->first();
        if ($driver) {
            return redirect()->to($this->getTemporaryFile($request->url));
        } else {
            abort(404);
        }
    }

    public function getPaperworkTemplate(Request $request)
    {
        $driver = Driver::where(DB::raw('crc32(concat(COALESCE(drivers.id, ""),COALESCE(drivers.email, ""),COALESCE(drivers.password, "")))'), $request->token)
            ->first();
        if ($driver) {
            $this->pdf($request, $request->id, $driver->id, $driver);
        } else {
            abort(404);
        }
    }
}
