<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * This is used by Laravel authentication to redirect users after login.
     *
     * @var string
     */
    public const HOME = '/dashboard';

    /**
     * The controller namespace for the application.
     *
     * When present, controller route declarations will automatically be prefixed with this namespace.
     *
     * @var string|null
     */
    // protected $namespace = 'App\\Http\\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            Route::prefix('api')
                ->middleware('api')
                ->namespace($this->namespace)
                ->group(base_path('routes/api.php'));

            Route::domain($this->baseDomain(env('ROUTE_SHIPPERS', 'shippers')))
                ->middleware('web')
                ->namespace($this->namespace)
                ->group(base_path('routes/shippers.php'));

            Route::domain($this->baseDomain(env('ROUTE_CARRIERS', 'carriers')))
                ->middleware('web')
                ->namespace($this->namespace)
                ->group(base_path('routes/carriers.php'));

            Route::domain($this->baseDomain(env('ROUTE_DRIVERS', 'drivers')))
                ->middleware('web')
                ->namespace($this->namespace)
                ->group(base_path('routes/drivers.php'));

            Route::middleware('web')
                ->namespace($this->namespace)
                ->group(base_path('routes/web.php'));
        });
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by(optional($request->user())->id ?: $request->ip());
        });
    }

    private function baseDomain(string $subdomain = ''): string
    {
        if (strlen($subdomain) > 0)
            $subdomain = "{$subdomain}.";

        return $subdomain . env('ROUTE_BASE');
    }
}
