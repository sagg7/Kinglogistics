<?php

namespace App\Enums;

abstract class LoadStatusEnum
{

    public const UNALLOCATED = 'unallocated';
    public const REQUESTED = 'requested';
    public const ACCEPTED = 'accepted';
    public const LOADING = 'loading';
    public const TO_LOCATION = 'to_location';
    public const ARRIVED = 'arrived';
    public const UNLOADING = 'unloading';
    public const FINISHED = 'finished';


}
