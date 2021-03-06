<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\LocationGroup;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting.blade.php channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int)$user->id === (int)$id;
});

Broadcast::channel('driver-location-king.{brokerId}', function ($user, $broker_id) {
    return (int)$user->broker_id === (int)$broker_id && $user->hasRole(['admin', 'operations', 'dispatch']);
});

Broadcast::channel('driver-location-carrier.{locationGroupId}', function ($carrier, $carrier_id) {
    // At this point, the authenticated user should be
   //return $carrier->id === LocationGroup::find($locationGroupId)->carrier_id;
    return (int)$carrier->id === (int)$carrier_id;
});

Broadcast::channel('driver-location-shipper.{locationGroupId}', function ($shipper, $shipper_id) {
   //return $shipper->id === LocationGroup::find($locationGroupId)->shipper_id;
    return (int)$shipper->id === (int)$shipper_id;
});

Broadcast::channel('chat.{broker_id}', function ($user, $broker_id) {
    return (int)$user->broker_id === (int)$broker_id && $user->hasRole(['admin', 'operations', 'dispatch']);
});

Broadcast::channel('load-status-update-web.{brokerId}', function ($user, $broker_id) {
    return (int)$user->broker_id === (int)$broker_id;
});

Broadcast::channel('load-status-update-carrier.{carrierId}', function ($carrier, $carrier_id) {
    return (int)$carrier->id === (int)$carrier_id;
});

Broadcast::channel('load-status-update-shipper.{shipperId}', function ($shipper, $shipper_id) {
    return (int)$shipper->id === (int)$shipper_id;
});

