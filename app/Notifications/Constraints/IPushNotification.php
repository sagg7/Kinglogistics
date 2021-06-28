<?php

namespace App\Notifications\Constraints;

interface IPushNotification
{
    /**
     * Get the notification body content
     *
     * @return array
     */
    public function notificationBody();

    /**
     * Get the notification data content
     *
     * @return array
     */
    public function notificationData();

    /**
     * Send notification to the user devices
     *
     * @return void
     */
    public function sendPushNotification();
}
