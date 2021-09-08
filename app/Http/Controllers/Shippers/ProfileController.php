<?php

namespace App\Http\Controllers\Shippers;

use App\Http\Controllers\Controller;
use App\Models\Equipment;
use App\Models\Service;
use App\Traits\QuillEditor\QuillHtmlRendering;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    use QuillHtmlRendering;

    protected $broker_id;

    public function __construct()
    {
        $this->broker_id = 1;
    }

    public function profile()
    {
        $shipper = auth()->user();
        $params = compact('shipper');
        return view('subdomains.shippers.profile.edit', $params);
    }

    public function equipment()
    {
        $equipment = Equipment::where('broker_id', $this->broker_id)
            ->first([
                'title',
                'message'
            ]);
        if (!$equipment) {
            $equipment = [];
        } else {
            $equipment->html = $this->renderHtmlString($equipment->message);
            $equipment = $equipment->toArray();
        }
        $params = ['section' => 'Equipment'] + $equipment;
        return view('layouts.renderHtml', $params);
    }

    public function services()
    {
        $service = Service::where('broker_id', $this->broker_id)
            ->first([
                'title',
                'message',
            ]);
        if (!$service) {
            $service = [];
        } else {
            $service->html = $this->renderHtmlString($service->message);
            $service = $service->toArray();
        }
        $params = ['section' => 'Services'] + $service;
        return view('layouts.renderHtml', $params);
    }
}
