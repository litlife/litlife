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

Broadcast::channel('user.{user_id}', function ($user, $user_id) {

	return true;
	//return (int)$user->getAuthIdentifier() === (int)$id;
});

Broadcast::channel('App.User.{user_id}', function ($user, $user_id) {

	return true;
	//return (int)$user->getAuthIdentifier() === (int)$id;
});
