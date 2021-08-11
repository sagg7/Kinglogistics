<?php

namespace App\Http\Controllers\Api;

use App\Events\NewChatMessage;
use App\Http\Controllers\Controller;
use App\Http\Resources\ConversationResource;
use App\Models\Driver;
use App\Models\Message;
use App\Models\Shipper;
use Illuminate\Http\Request;
use App\Traits\Notifications\PushNotificationsTrait;
use App\Enums\DriverAppRoutes;


class ChatController extends Controller
{

    use PushNotificationsTrait;

    public function getConversation(Request $request)
    {
        $driverId = $request->get('driver_id');
        $driver = $driverId ? Driver::find($driverId) : auth()->user();

        $messages = ConversationResource::collection($driver->messages);

        return response(['status' => 'ok', 'messages' => $messages], 200);
    }

    public function sendMessageAsUser(Request $request)
    {
        $driverIds = $request->get('driver_ids');
        $content = $request->get('content');
        $userId = $request->get('user_id');

        if (empty($driverIds)) {
            return response('Drivers collection is not setted', 400);
        }

        foreach ($driverIds as $driverId) {
            $driver = Driver::find($driverId);

            $this->sendMessage(
                $content,
                $driverId,
                $userId,
                null
            );

            $driverDevices = $this->getUserDevices($driver);

            $this->sendNotification(
                'Message from King',
                $content,
                $driverDevices,
                DriverAppRoutes::CHAT
            );
        }

        return response(['status' => 'ok'], 200);
    }

    public function sendMessageAsDriver(Request $request)
    {
        $driver = auth()->user();

        $this->sendMessage(
            $request->get('content'),
            $driver->id,
            null,
            true
        );

        return response(['status' => 'ok'], 200);
    }

    // ....

    private function sendMessage(
        string $content,
        int $driverId,
        int $userId = null,
        bool $isDriverSender = null): Message
    {
        $message = Message::create([
            'content' => $content,
            'driver_id' => $driverId,
            'user_id' => $userId,
            'is_driver_sender' => $isDriverSender,
        ]);

        event(new NewChatMessage($message));

        return $message;
    }

}
