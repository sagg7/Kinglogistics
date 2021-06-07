<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('auth.login');
    }

    /**
     * @return string
     */
    private function getGuard(): string
    {
        $subdomain = explode('.', request()->getHost())[0];
        switch ($subdomain) {
            case env('ROUTE_SHIPPERS'):
                $guard = 'shipper';
                break;
            case env('ROUTE_CARRIERS'):
                $guard = 'carrier';
                break;
            case env('ROUTE_DRIVERS'):
                $guard = 'driver';
                break;
            default:
                $guard = 'web';
                break;
        }

        return $guard;
    }

    /**
     * Handle an incoming authentication request.
     *
     * @param  \App\Http\Requests\Auth\LoginRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(LoginRequest $request)
    {
        $request->authenticate($this->getGuard());

        $request->session()->regenerate();

        session(['timezone' => $request->timezone]);

        return redirect()->intended(RouteServiceProvider::HOME);
    }

    /**
     * Destroy an authenticated session.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {
        Auth::guard($this->getGuard())->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
