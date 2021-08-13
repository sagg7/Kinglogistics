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
    // TODO: Check if user is admin
    return $user;
});

Broadcast::channel('driver-location-carrier.{locationGroupId}', function ($carrier, $locationGroupId) {
    // At this point, the authenticated user should be
   return $carrier->id === LocationGroup::find($locationGroupId)->carrier_id;
});

Broadcast::channel('driver-location-shipper.{locationGroupId}', function ($shipper, $locationGroupId) {
   return $shipper->id === LocationGroup::find($locationGroupId)->shipper_id;
});

Broadcast::channel('chat', function ($user) {
    // TODO: Check if user is admin
    return true;
});

