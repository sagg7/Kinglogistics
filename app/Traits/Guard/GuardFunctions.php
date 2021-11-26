<?php

namespace App\Traits\Guard;

trait GuardFunctions
{
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
}
