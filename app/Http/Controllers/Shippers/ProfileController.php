<?php

namespace App\Http\Controllers\Shippers;

use App\Http\Controllers\Controller;

class ProfileController extends Controller
{

    public function profile()
    {
        $shipper = auth()->user();
        $params = compact('shipper');
        return view('subdomains.shippers.profile.edit', $params);
    }
}
