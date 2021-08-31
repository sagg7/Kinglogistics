<?php

namespace App\Http\Controllers\Api;

use App\Events\NewChatMessage;
use App\Http\Controllers\Controller;
use App\Http\Resources\ConversationResource;
use App\Models\Driver;
use App\Models\Message;
use App\Models\Shipper;
use App\Traits\Chat\MessagesTrait;
use Illuminate\Http\Request;
use App\Traits\Notifications\PushNotificationsTrait;
use App\Enums\DriverAppRoutes;


class ChatController extends Controller
{

    use PushNotificationsTrait, MessagesTrait;

    public function getConversation(Request $request)
    {
        $driverId = $request->get('driver_id');
        $driver = $driverId ? Driver::find($driverId) : auth()->user();

        $messages = $driver
            ->messages()
            ->orderBy('id', 'desc')
            ->paginate(20);

        $conversation = ConversationResource::collection($messages);

        return response(['status' => 'ok', 'messages' => $conversation], 200);
    }

    public function sendMessageAsUser(Request $request)
    {
        $driverIds = $request->get('driver_ids');
        $content = $request->get('content');
        $userId = $request->get('user_id');
        $image = $request->get('image');

        if (empty($driverIds)) {
            return response('Drivers collection is not setted', 400);
        }

        if (!empty($image)) {
            $image = $this->uploadImage($image, 'chat');
        }

        foreach ($driverIds as $driverId) {
            $driver = Driver::find($driverId);

            $message = $this->sendMessage(
                $content,
                $driverId,
                $userId,
                null,
                null,
                null,
                $image
            );

            $driverDevices = $this->getUserDevices($driver);

            $this->sendNotification(
                'Message from King',
                $content,
                $driverDevices,
                DriverAppRoutes::CHAT,
                $message,
                DriverAppRoutes::CHAT_ID,
            );
        }

        return response(['status' => 'ok'], 200);
    }

    public function sendMessageAsDriver(Request $request)
    {
        $driver = auth()->user();
        $content = $request->get('content');
        $image = $request->get('image');

        if (!empty($image)) {
            $image = $this->uploadImage($image, 'chat');
        }

        $message = $this->sendMessage(
            $content,
            $driver->id,
            null,
            true,
            null,
            null,
            $image
        );

        event(new NewChatMessage($message));

        return response(['status' => 'ok'], 200);
    }

}
