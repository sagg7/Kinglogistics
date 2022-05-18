<?php

namespace App\Http\Middleware;

use App\Helpers\BrokerHelper;
use Closure;
use Illuminate\Http\Request;

class BrokerCheck
{
    protected  $helper;

    public function __construct()
    {
        $this->helper = new BrokerHelper();
    }

    public function handle(Request $request, Closure $next)
    {
        $expired = $this->helper->isExpired();

        if ($expired) {
            return redirect()->route('company.expired');
        }

        if (!session('broker')) {
            session(['broker' => auth()->user()->broker_id]);
        }

        return $next($request);
    }
}
