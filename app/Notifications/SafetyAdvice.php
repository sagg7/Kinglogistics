<?php

namespace App\Notifications;

use App\Notifications\Constraints\IPushNotification;
use App\Traits\Notifications\PushNotificationsTrait;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class SafetyAdvice extends Notification implements IPushNotification
{
    use Queueable, PushNotificationsTrait;

    private $driver;
    private $advice;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($driver, $advice)
    {
        $this->driver = $driver;
        $this->advice = $advice;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        // ...
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        // TODO: Add the notification body to be stored in DB
        return [
            //
        ];
    }

    public function notificationBody()
    {
        return [
            'title' => '',
            'message' => ''
        ];
    }

    public function notificationData()
    {
        return [

        ];
    }

    public function sendPushNotification()
    {
        $tokens = $this->getUserDevices($this->driver);

        if (!count($tokens))
            return;

        $notification = $this->notificationBody();

        $data = [
            'smart_link' => ''
        ];

        $this->sendNotification(
            $notification['title'],
            $notification['message'],
            $tokens,
            $data
        );
    }

}
