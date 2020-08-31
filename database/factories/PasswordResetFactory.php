<?php

use App\PasswordReset;
use Faker\Generator as Faker;

$factory->define(App\PasswordReset::class, function (Faker $faker) {

	return [
		'user_id' => 0,
		'email' => '',
		'used_at' => null,
		'created_at' => now(),
		'updated_at' => now()
	];
});

$factory->state(App\PasswordReset::class, 'with_user_with_confirmed_email', function ($faker) {

	$user = factory(App\User::class)
		->states('with_confirmed_email')
		->create();

	$email = $user->emails()->first();

	return [
		'user_id' => $user->id,
		'email' => $email->email,
	];
});

$factory->afterMakingState(App\PasswordReset::class, 'used', function (PasswordReset $passwordReset, $faker) {
	$passwordReset->used();
});
