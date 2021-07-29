<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Drivers\NotificationResource;
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

    public function getSafetyAdvicesList()
    {
        $driver = auth()->user();

        $advices = $driver->notifications->filter(function ($notification) {
            return $notification->type === SafetyAdvice::class;
        })->values();

        $advices = NotificationResource::collection($advices);

        return response(compact('advices'), 200);
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
