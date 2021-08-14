<?php

namespace App\Traits\Chat;

use App\Models\Message;

trait MessagesTrait
{
    private function sendMessage(
        string $content,
        int $driverId,
        int $userId = null,
        bool $isDriverSender = null): Message
    {
        return Message::create([
            'content' => $content,
            'driver_id' => $driverId,
            'user_id' => $userId,
            'is_driver_sender' => $isDriverSender,
        ]);
    }
}
