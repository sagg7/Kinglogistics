<?php

namespace App\Http\Controllers\Shippers;

use App\Http\Controllers\Controller;
use App\Models\Broker;
use App\Models\Equipment;
use App\Models\Service;
use App\Traits\QuillEditor\QuillHtmlRendering;
use Illuminate\Http\Request;

class BrokerController extends Controller
{
    use QuillHtmlRendering;

    protected $broker_id;

    public function __construct()
    {
        $this->broker_id = 1;
    }

    public function index()
    {
        $broker = Broker::findOrFail($this->broker_id);
        $params = compact('broker');
        return view('subdomains.shippers.broker.index', $params);
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
        return $equipment;
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
        return $service;
    }
}
