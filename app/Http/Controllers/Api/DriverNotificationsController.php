<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Drivers\NotificationResource;
use App\Http\Resources\Drivers\SafetyAdviceResource;
use App\Models\SafetyMessage;
use App\Notifications\SafetyAdvice;
use Illuminate\Http\Request;

class DriverNotificationsController extends Controller
{

    public function index()
    {
        $driver = auth()->user();

        $notifications = NotificationResource::collection($driver->notifications);

        return response(compact('notifications'), 200);
    }

    public function getSafetyAdvicesNotifications()
    {
        $driver = auth()->user();

        $advices = $driver->notifications->filter(function ($notification) {
            return $notification->type === SafetyAdvice::class;
        })->values();

        $advices = NotificationResource::collection($advices);

        return response(compact('advices'), 200);
    }

    public function getUnreadSafetyAdvicesNotifications()
    {
        $driver = auth()->user();

        $advices = $driver->notifications->filter(function ($notification) {
            return $notification->type === SafetyAdvice::class && empty($notification->read_at);
        })->values();

        $advices = NotificationResource::collection($advices);

        return response(compact('advices'), 200);
    }

    public function getSafetyAdvice($notificationId)
    {
        $driver = auth()->user();

        $notification = $driver->notifications->filter(function ($notification) use ($notificationId) {
            return $notification->id == $notificationId;
        })->first();

        if (!$notification) {
            return response(['status' => 'error', 'message' => 'Advice not found'], 404);
        }

        $advice = SafetyMessage::find($notification->data['message']['id']);

        return response(['status' => 'ok', 'data' => new SafetyAdviceResource($advice)]);
    }

    public function markNotificationAsRead(Request $request)
    {
        $driver = auth()->user();

        if (empty($notificationId = $request->get('id'))) {
            return response(["message" => "Not Found"], 404);
        }

        $notification = $driver->notifications->first(function ($notification) use ($notificationId) {
            return $notification->id === $notificationId;
        });

        if (empty($notification)) {
            return response(["message" => "Not Found"], 404);
        }

        $notification->markAsRead();

        return response(["message" => "Success"], 200);
    }

}
