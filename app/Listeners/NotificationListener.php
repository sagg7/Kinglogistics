<?php

namespace App\Listeners;

use App\Enums\DriverAppRoutes;
use App\Notifications\Constraints\IPushNotification;
use App\Notifications\SafetyAdvice;
use App\Traits\Notifications\PushNotificationsTrait;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Events\NotificationSent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;


class NotificationListener implements IPushNotification
{
    use PushNotificationsTrait;

    private $driver;
    private $message;
    private $notification;

    /**
     * Create the event listener
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param NotificationSent $event
     * @return void
     */
    public function handle(NotificationSent $event)
    {
        if (get_class($event->notification) == SafetyAdvice::class) {
            $this->driver = $event->notifiable;
            $this->message = $event->response->data['message'];
            $this->notification = $event->notification;

            $this->sendPushNotification();
            Log::info('Safety advice notification sent');
        }
    }

    public function notificationBody()
    {
        return [
            'title' => 'Read the next information carefully!',
            'message' => $this->message['title'],
        ];
    }

    public function sendPushNotification()
    {
        $tokens = $this->getUserDevices($this->driver);

        if (!count($tokens))
            return;

        $notification = $this->notificationBody();
        $body = $this->notificationData();

        $this->sendNotification(
            $notification['title'],
            $notification['message'],
            $tokens,
            DriverAppRoutes::SAFETY,
            $body,
            DriverAppRoutes::SAFETY_ID,
        );
    }

    public function notificationData(): array
    {
        return [
            'id' => $this->notification->id,
            'data' => [
                'id' => $this->message['id'],
                'title' => $this->message['title'],
                'message' => '',
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            ]
        ];
    }
}
