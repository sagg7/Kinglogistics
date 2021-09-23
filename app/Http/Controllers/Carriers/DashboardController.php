<?php

namespace App\Http\Controllers\Carriers;

use App\Http\Controllers\Controller;
use App\Traits\Tracking\TrackingTrait;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    use TrackingTrait;

    public function index()
    {
        $params = [
            'tracking' => $this->getTrackingData(),
        ];

        return view('subdomains.carriers.dashboard', $params);
    }
}
