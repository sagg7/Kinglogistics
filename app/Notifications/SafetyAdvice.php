<?php

namespace App\Notifications;

use App\Enums\DriverAppRoutes;
use App\Notifications\Constraints\IPushNotification;
use App\Traits\Notifications\PushNotificationsTrait;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class SafetyAdvice extends Notification
{
    use Queueable;

    private $driver;
    private $message;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($driver, $message)
    {
        $this->driver = $driver;
        $this->message = $message;
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
        return $this->notificationData();
    }

    public function notificationData()
    {
        return [
            'message' => $this->message,
        ];
    }

}
