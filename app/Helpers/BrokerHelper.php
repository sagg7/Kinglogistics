<?php

namespace App\Helpers;

use App\Models\Broker;
use Carbon\Carbon;

class BrokerHelper
{
    /**
     * @return mixed
     */
    private function getBroker()
    {
        return Broker::find(session('broker'));
    }

    /**
     * @return mixed
     */
    public function getExpirationDate()
    {
        return $this->getBroker()->expiration_date;
    }

    /**
     * @return bool
     */
    public function isExpired(): bool
    {
        if (session('broker')) {
            // Get broker data
            $broker = $this->getBroker();

            // If the broker has been manually disabled
            if ($broker->disabled) {
                return true;
            }
            // If it has no expiration date, always enabled
            if (!$broker->expiration_date) {
                return false;
            } else {
                $today = Carbon::now()->startOfDay();
                $expiration = Carbon::parse($broker->expiration_date);
                return $today->greaterThan($expiration);
            }
        }
        return false;
    }
}
