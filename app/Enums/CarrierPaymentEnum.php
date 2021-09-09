<?php

namespace App\Enums;

abstract class CarrierPaymentEnum
{

    public const PENDING = 'pending';
    public const APPROVED = 'approved';
    public const COMPLETED = 'completed';
    public const CHARGES = 'charges';

}
