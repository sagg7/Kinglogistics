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

Broadcast::channel('driver-location-king', function ($user) {
    // TODO: Check if user is admin, operations or dispatch
    return (bool)$user->hasRole(['admin', 'operations', 'dispatch']);
});

Broadcast::channel('driver-location-carrier.{locationGroupId}', function ($carrier, $carrier_id) {
    // At this point, the authenticated user should be
   //return $carrier->id === LocationGroup::find($locationGroupId)->carrier_id;
    return $carrier->id === (int)$carrier_id;
});

Broadcast::channel('driver-location-shipper.{locationGroupId}', function ($shipper, $shipper_id) {
   //return $shipper->id === LocationGroup::find($locationGroupId)->shipper_id;
    return $shipper->id === (int)$shipper_id;
});

Broadcast::channel('chat', function ($user) {
    // TODO: Check if user is admin, operations or dispatch
    return (bool)$user->hasRole(['admin', 'operations', 'dispatch']);
});

