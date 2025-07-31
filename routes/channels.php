<?php

use App\Models\ChatRoom;
use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('chat.{chatRoomId}', function ($user, $chatRoomId) {
    $chatRoom = ChatRoom::find($chatRoomId);
    return $chatRoom && ($chatRoom->user_one_id === $user->user_id || $chatRoom->user_two_id === $user->user_id);
});
