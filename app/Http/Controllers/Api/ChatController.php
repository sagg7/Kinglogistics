<?php

namespace App\Http\Controllers\Api;

use App\Events\NewChatMessage;
use App\Http\Controllers\Controller;
use App\Http\Resources\ConversationResource;
use App\Models\Driver;
use App\Models\Message;
use App\Traits\Chat\MessagesTrait;
use App\Traits\EloquentQueryBuilder\GetSelectionData;
use Illuminate\Http\Request;
use App\Traits\Notifications\PushNotificationsTrait;
use App\Enums\DriverAppRoutes;


class ChatController extends Controller
{

    use GetSelectionData, PushNotificationsTrait, MessagesTrait;

    public function getChatHistory(Request $request)
    {
        $driver = auth()->user();

        $query = Message::where('driver_id', $driver->id)
            ->orderBy('id', 'desc');

//        $this->readMessages($driver_id, auth()->user()->id);

        $messages = $this->selectionData($query, $request->take, $request->page);

        return response(['status' => 'ok', 'data' => $messages]);
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
                $driverId,
                $content,
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
            $driver->id,
            $content,
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
