<?php

namespace App\Http\Controllers\Api;

use App\Events\NewChatMessage;
use App\Http\Controllers\Controller;
use App\Http\Resources\ConversationResource;
use App\Models\Driver;
use App\Models\Message;
use App\Models\Shipper;
use Illuminate\Http\Request;

class ChatController extends Controller
{

    public function getConversation(Request $request)
    {
        $driverId = $request->get('driver_id');
        $driver = $driverId ? Driver::find($driverId) : auth()->user();

        $messages = ConversationResource::collection($driver->messages);

        return response(['status' => 'ok', 'messages' => $messages], 200);
    }

    public function sendMessageAsShipper(Request $request)
    {
        $this->sendMessage(
            $request->get('content'),
            $request->get('driver_id'),
            $request->get('shipper_id'),
            $request->get(Shipper::class),
            null
        );

        return response(['status' => 'ok'], 200);
    }

    public function sendMessageAsDriver(Request $request)
    {
        $driver = auth()->user();

        $this->sendMessage(
            $request->get('content'),
            $driver->id,
            null,
            null,
            true
        );

        return response(['status' => 'ok'], 200);
    }

    // ....

    private function sendMessage(
        string $content,
        int $driverId,
        int $messageableId = null,
        string $messageableType = null,
        bool $isDriverSender = null): Message
    {
        $message = Message::create([
            'content' => $content,
            'driver_id' => $driverId,
            'messageable_type' => $messageableType,
            'messageable_id' => $messageableId,
            'is_driver_sender' => $isDriverSender,
        ]);

        event(new NewChatMessage($message));

        return $message;
    }

}
