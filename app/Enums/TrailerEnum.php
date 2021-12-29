<?php

namespace App\Enums;

abstract class TrailerEnum
{
    public const AVAILABLE = 'available';
    public const RENTED = 'rented';
    public const RETURNED = 'returned';
    public const OUT_OF_SERVICE = 'oos';
}
