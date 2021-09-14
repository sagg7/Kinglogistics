<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Timezone
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $prefix = $request->route()->action["prefix"];
        switch ($prefix) {
            case '/chat':
                break;
            default:
                if (session('timezone'))
                    date_default_timezone_set(session('timezone'));
                break;
        }
        return $next($request);
    }
}
