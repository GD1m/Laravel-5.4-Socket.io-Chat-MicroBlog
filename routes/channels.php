<?php

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

Broadcast::channel('chat-channel-online', function ($user) {
    return [
        'id' => $user->id,
        'username' => $user->username,
    ];
});

Broadcast::channel('chat-channel.{id}', function ($user, $id) {
    return $user->id === $id;
});
