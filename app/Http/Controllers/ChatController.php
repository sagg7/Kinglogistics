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

    public function index()
    {
        $contacts = Driver::where(function ($q) {
            $q->whereHas('latestMessage', function ($r) {
                //$r->where('user_id', auth()->user()->id);
            })
                ->orWhereHas('shift');
        })
            ->whereNull("inactive")
            ->with([
                'latestMessage' => function ($q) {
                    $q->select(['id',DB::raw('SUBSTRING(content, 1, 100) as content'),'created_at','driver_id', 'is_driver_sender']);
                },
                /*'shifts'/* => function ($q) {
                    $q->skip(0)->take(15);
                },*/
                /*'carrier:id,name',
                'truck' => function ($q) {
                    $q->with('trailer:id,number')
                        ->select(['id', 'number', 'driver_id', 'trailer_id']);
                },*/
            ])
            ->withCount([
                'messages as unread_count' => function ($q) {
                    $q->where('user_unread', 1);
                }
            ])
            ->get([
                'id',
                'carrier_id',
                'name',
            ]);
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
        $driver_id= $request->driver_id;
        $content = $request->message;
        $user_id = auth()->user()->id;

        $message = new Message();
        $message->content = $content;
        $message->driver_id = $driver_id;
        $message->user_id = $user_id;
        $message->is_driver_sender = null;
        $message->save();

        $this->sendMessage(
            $content,
            $driver_id,
            $user_id,
            null,
            null,
            true,
        );

        $driver = Driver::find($driver_id);

        $driverDevices = $this->getUserDevices($driver);

        $this->sendNotification(
            'Message from King',
            $content,
            $driverDevices,
            DriverAppRoutes::CHAT
        );

        return ['success' => true, 'message' => $message];
    }
}
