<?php

namespace App\Traits\Timezone;

trait SetDefaultLocalTimezone
{
    private function setDefaultLocalTimezone()
    {
        if (session('timezone')) {
            date_default_timezone_set(session('timezone'));
        }
    }
}
