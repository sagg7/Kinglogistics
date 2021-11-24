<?php

namespace App\Http\Controllers;

use App\Models\Broker;
use App\Models\Paperwork;
use App\Traits\Guard\GuardFunctions;
use App\Traits\Paperwork\PaperworkFilesFunctions;
use Illuminate\Http\Request;

class DocumentationController extends Controller
{
    use GuardFunctions, PaperworkFilesFunctions;

    protected $broker_id;

    public function __construct()
    {
        $this->broker_id = 1;
    }

    public function index()
    {
        $guard = $this->getGuard();
        $user = auth()->guard($guard)->user();
        $paperwork = Paperwork::whereNotNull('template')
            ->where('type', $guard)
            ->whereNull('category')
            ->where('required', 1)
            ->orderBy('required', 'DESC')
            ->get(['id', 'name', 'required']);
        $paperworkTemplates = $this->getTemplatesPaperwork($paperwork, $user->id);
        foreach ($paperwork as $item) {
            if (!isset($paperworkTemplates[$item->id])) {
                return redirect()->route('paperwork.showTemplate', [$item->id, $user->id]);
            }
        }

        $hasOrientation = Paperwork::whereNotNull('template')
            ->where('type', $guard)
            ->whereNotNull('category')
            ->where('required', 1)
            ->orderBy('required', 'DESC')
            ->first();
        if ($hasOrientation) {
            $broker = Broker::findOrFail($this->broker_id);
            $text = "The first section of the paperwork has been completed.<br> Contact <strong>$broker->name</strong> to start the orientation paperwork.";
        } else {
            $text = "Shortly we'll be in contact with you to continue the process.";
        }
        return view('documentation.index', compact('text'));
    }
}
