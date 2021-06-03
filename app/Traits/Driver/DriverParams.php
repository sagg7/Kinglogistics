<?php

namespace App\Traits\Driver;

trait DriverParams
{
    private function getTurnsArray()
    {
        // 6 AM TO 6 PM
        // 6 PM TO 6 AM
        return [
            'turns' => [null => '', 'Morning', 'Night'],
        ];
    }
}
