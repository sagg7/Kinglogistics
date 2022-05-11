<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\BrokerHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\Carrier;
use App\Models\Driver;
use App\Providers\RouteServiceProvider;
use App\Traits\Guard\GuardFunctions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AuthenticatedSessionController extends Controller
{
    use GuardFunctions;

    protected $dHelper;

    public function __construct()
    {
        $this->dHelper = new BrokerHelper();
    }

    /**
     * Display the login view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('auth.login');
    }

    private function attemptLogin(LoginRequest $request, $guard = null)
    {
        $request->authenticate($guard ?: $this->getGuard());

        $request->session()->regenerate();

        session(['timezone' => $request->timezone]);
        session(['broker' => auth()->user()->broker_id]);
    }

    /**
     * Handle an incoming authentication request.
     *
     * @param  \App\Http\Requests\Auth\LoginRequest  $request
     * @return array|\Illuminate\Http\RedirectResponse
     */
    public function store(LoginRequest $request)
    {
        $this->attemptLogin($request);

        if ($request->ajax()) {
            return ['success' => true, 'route' => RouteServiceProvider::HOME];
        }

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

    public function tokenLogin(Request $request)
    {
        $token = $request->token;
        $guard = $this->getGuard();
        $loginReq = new LoginRequest();
        $loginReq->setMethod('POST');
        switch ($guard) {
            case 'carrier':
                $user = Carrier::where(DB::raw('crc32(concat(COALESCE(carriers.id, ""),COALESCE(carriers.password, "")))'), $token)
                    ->first();
                break;
            case 'driver':
                $user = Driver::where(DB::raw('crc32(concat(COALESCE(drivers.id, ""),COALESCE(drivers.password, "")))'), $token)
                    ->first();
                break;
            default:
                $user = null;
                break;
        }
        if (!$user) {
            abort(404);
        }
        $loginReq->request->add(['email' => $user->email, 'password' => $user->password]);
        if (! Auth::guard($guard)->loginUsingId($user->id)) {
            abort(404);
        }
        session(['fillDocumentation' => true]);

        return redirect()->intended(RouteServiceProvider::HOME);
    }
}
