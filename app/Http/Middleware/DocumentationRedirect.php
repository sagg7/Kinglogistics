<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class DocumentationRedirect
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
        if (session('fillDocumentation')) {
            if ($request->route()->uri() === 'dashboard')
                return redirect('/documentation');
        }
        return $next($request);
    }
}
