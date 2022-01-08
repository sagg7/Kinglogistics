<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;

class PermissionMiddleware
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
     * @param Request $request
     * @param Closure $next
     * @param  $permissions
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $permissions)
    {
        // OR has role
        $permissionRole = (explode('||', $permissions));
        // Default flag value is false
        $roleFlag = false;
        // If we find a role statement "||role:"
        if (count($permissionRole) > 1) {
            // Get the roles string
            $roles = array_values(array_filter(explode('role:', $permissionRole[1])))[0];
            // Explode the string and check if user has role
            if ($request->user()->hasRole(explode('|', $roles)))
                // Set flag as true if user has the role
                $roleFlag = true;
        }

        if (($this->auth->guest() || !$request->user()->can(explode('|', $permissions))) && !$roleFlag) {
            abort(403);
        }

        return $next($request);
    }
}
