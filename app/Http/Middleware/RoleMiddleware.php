<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;

class RoleMiddleware
{
    protected $auth;

    /**
     * Creates a new instance of the middleware.
     *
     * @param Guard $auth
     */
    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param  Request $request
     * @param  Closure $next
     * @param  $roles
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $roles)
    {
        if ($this->auth->guest() || !$request->user()->hasRole(explode('|', $roles))) {
            abort(403);
        }

        return $next($request);
    }
}
