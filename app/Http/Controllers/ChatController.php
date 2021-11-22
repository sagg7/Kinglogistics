<?php

namespace App\Http\Controllers;

use App\Enums\DriverAppRoutes;
use App\Models\Driver;
use App\Models\Message;
use App\Traits\Chat\MessagesTrait;
use App\Traits\EloquentQueryBuilder\GetSelectionData;
use App\Traits\Notifications\PushNotificationsTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChatController extends Controller
{
    use GetSelectionData, PushNotificationsTrait, MessagesTrait;

    /**
     * @return mixed
     */
    public function getContacts()
    {
        return Driver::where(function ($q) {
            $q->whereHas('latestMessage', function ($r) {
                //$r->where('user_id', auth()->user()->id);
            })
                ->orWhereHas('shift');
        })
            ->leftJoin('messages', function ($q) {
                $q->on('messages.driver_id', '=', 'drivers.id')
                    ->on('messages.id', '=', DB::raw('(select max(id) from messages where messages.driver_id = drivers.id)'));
            })
            ->whereNull("inactive")
            ->with([
                'latestMessage' => function ($q) {
                    $q->select(['id',DB::raw('SUBSTRING(content, 1, 100) as content'),'created_at','driver_id', 'is_driver_sender']);
                },
            ])
            ->withCount([
                'messages as unread_count' => function ($q) {
                    $q->where('user_unread', 1);
                }
            ])
            ->orderBy('messages.created_at', 'DESC')
            ->get();
    }

    public function index()
    {
        $contacts = $this->getContacts();
        $params = compact('contacts');
        return view('chat.index', $params);
    }

    public function getChatHistory(Request $request)
    {
        $driver_id = $request->driver_id;

        $query = Message::where('driver_id', $driver_id)
            //->where('user_id', auth()->user()->id)
            ->orderBy('id', 'desc');

        $this->readMessages($driver_id, auth()->user()->id);

        return $this->selectionData($query, $request->take, $request->page);
    }

    public function sendMessageAsUser(Request $request)
    {
        $drivers = $request->drivers;
        $content = $request->message;
        $image = $request->image;
        $is_bot_sender = $request->is_bot_sender;
        if ($is_bot_sender)
            $user_id = null;
        else
            $user_id = auth()->user()->id;

        $messages = [];
        foreach ($drivers as $driver_id) {
            if ($image)
                $image = $this->uploadImage($image, 'chat');

            $message = $this->sendMessage(
                $driver_id,
                $content,
                $user_id,
                null,
                null,
                true,
                $image,
                $is_bot_sender
            );

            $driver = Driver::find($driver_id);

            $driverDevices = $this->getUserDevices($driver);

            $this->sendNotification(
                'Message from King',
                $content,
                $driverDevices,
                DriverAppRoutes::CHAT,
                $message,
                DriverAppRoutes::CHAT_ID,
            );
            $messages[] = $message;
        }

        return ['success' => true, 'messages' => $messages];
    }
}
