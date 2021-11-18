<?php

namespace App\Traits\Chat;

use App\Models\Message;
use App\Traits\Storage\FileUpload;

trait MessagesTrait
{

    use FileUpload;

    private function sendMessage(
        int $driverId,
        string $content = null,
        int $userId = null,
        bool $isDriverSender = null,
        bool $userUnread = null,
        bool $driverUnread = null,
        string $imageUrl = null,
        string $botSended = null
    ): Message
    {
        return Message::create([
            'content' => $content,
            'driver_id' => $driverId,
            'user_id' => $userId,
            'is_driver_sender' => $isDriverSender,
            'user_unread' => $userUnread,
            'driver_unread' => $driverUnread,
            'image' => $imageUrl,
            'is_bot_sender' => $botSended,
        ]);
    }

    private function readMessages(
        int $driverId,
        int $userId = null
    )
    {
        $fromUser = (bool)$userId;
        if ($fromUser) {
            $updateData = ['user_unread' => null];
        } else {
            $updateData = ['driver_unread' => null];
        }
        Message::where(function ($q) use ($fromUser) {
            if ($fromUser) {
                $q->where('user_unread', 1);
            } else {
                $q->where('driver_unread', 1);
            }
        })
            ->where('driver_id', $driverId)
            ->update($updateData);
    }
}
