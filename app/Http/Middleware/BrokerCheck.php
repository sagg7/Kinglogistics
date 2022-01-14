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
        // TODO: Handle expiration case
        /*$expired = $this->helper->isExpired();

        if ($expired) {
            // TODO: redirect to expiration view
        }*/


        if (!session('broker')) {
            $broker_id = auth()->user()->load('broker')->broker->id;
            session(['broker' => $broker_id]);
        }

        return $next($request);
    }
}
