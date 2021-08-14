<?php

namespace App\Traits\Driver;

use App\Models\Turn;

trait DriverParams
{
    private function getTurnsArray()
    {
        // 6 AM TO 6 PM
        // 6 PM TO 6 AM
        return [
            'turns' => [null => 'Select'] + Turn::pluck('name', 'id')->toArray(),
        ];
    }
}
