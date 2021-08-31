<?php

namespace App\Notifications;

use App\Enums\DriverAppRoutes;
use App\Notifications\Constraints\IPushNotification;
use App\Traits\Notifications\PushNotificationsTrait;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LoadAssignment extends Notification implements IPushNotification
{
    use Queueable, PushNotificationsTrait;

    private $driver;
    private $load;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($driver, $load)
    {
        $this->driver = $driver;
        $this->load = $load;
        $this->sendPushNotification();
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

    public function notificationBody()
    {
        return [
            'title' => 'New load assignment!',
            'message' => 'You have been assigned to a new load, tap to see details.'
        ];
    }

    public function notificationData()
    {
        return [
            'load' => $this->load,
        ];
    }

    public function sendPushNotification()
    {
        $tokens = $this->getUserDevices($this->driver);

        if (!count($tokens))
            return;

        $notification = $this->notificationBody();

        $data = $this->notificationData();

        $this->sendNotification(
            $notification['title'],
            $notification['message'],
            $tokens,
            DriverAppRoutes::LOAD . "?id=" . $this->load->id,
            $data,
            DriverAppRoutes::LOAD_ID
        );
    }
}
