<?php

namespace App\Traits\Notifications;

use App\Models\Device;
use App\Notifications\Constraints\IPushNotification;
use LaravelFCM\Message\Exceptions\InvalidOptionsException;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use LaravelFCM\Facades\FCM;
use Illuminate\Support\Facades\Log;


trait PushNotificationsTrait
{

    public function sendNotification($title, $message, $token, $targetScreen, $data = [], $targetScreenName = null)
    {
        if (!$token) {
            return;
        }

        $optionBuilder = $this->optionBuild();

        $notificationBuilder = new PayloadNotificationBuilder($title);
        $notificationBuilder
            ->setBody($message)
            ->setChannelId('notifications')
            ->setSound('default');


        $dataBuilder = new PayloadDataBuilder();
        $dataBuilder->addData([
            'click_action' => IPushNotification::CLICK_ACTION,
            'screen' => $targetScreen,
            'target_screen' => $targetScreenName,
            'payload' => $data,
        ]);

        $option = $optionBuilder->build();
        $notification = $notificationBuilder->build();
        $data = $dataBuilder->build();

        $downstreamResponse = FCM::sendTo($token, $option, $notification, $data);

            if ($downstreamResponse->numberFailure() > 0) {
            foreach ($downstreamResponse->tokensToDelete() as $token) {
                Device::where('token', $token)->delete();
            }
        }
    }

    private function optionBuild()
    {
        try {
            $optionBuilder = new OptionsBuilder();
            $optionBuilder->setTimeToLive(60 * 20);
            return $optionBuilder;

        } catch (InvalidOptionsException $exception) {
            log($exception->getMessage());
        }
    }

    public function getUserDevices($driver)
    {
        return $driver->devices->pluck('token')->all();
    }

}
