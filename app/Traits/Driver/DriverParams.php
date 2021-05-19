<?php

namespace App\Traits\Driver;

trait DriverParams
{
    private function getTurnsArray()
    {
        return [
            'turns' => [null => '', 'Morning', 'Night'],
        ];
    }
}
