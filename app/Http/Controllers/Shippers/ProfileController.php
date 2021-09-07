<?php

namespace App\Http\Controllers\Shippers;

use App\Http\Controllers\Controller;
use App\Models\Equipment;
use App\Models\Service;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
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
                'message as html'
            ])
            ->toArray();
        $params = ['section' => 'Equipment'] + $equipment;
        return view('layouts.renderHtml', $params);
    }

    public function services()
    {
        $equipment = Service::where('broker_id', $this->broker_id)
            ->first([
                'title',
                'message as html'
            ])
            ->toArray();
        $params = ['section' => 'Services'] + $equipment;
        return view('layouts.renderHtml', $params);
    }
}
