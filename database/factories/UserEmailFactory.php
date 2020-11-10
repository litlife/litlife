<?php

use Carbon\Carbon;
use Faker\Generator as Faker;

$factory->define(App\UserEmail::class, function (Faker $faker) {

    return [
        'user_id' => function () {
            return factory(App\User::class)->state('without_email')->create()->id;
        },
        'email' => uniqid() . '@' . uniqid() . '.com',
        'confirm' => rand('0', '1'),
        'show_in_profile' => rand('0', '1'),
        'rescue' => rand('0', '1'),
        'notice' => rand('0', '1')
    ];
});

$factory->afterMakingState(App\UserEmail::class, 'confirmed', function ($email, $faker) {
    $email->confirm = true;
});

$factory->afterMakingState(App\UserEmail::class, 'not_confirmed', function ($email, $faker) {
    $email->confirm = false;
});

$factory->afterMakingState(App\UserEmail::class, 'noticed', function ($email, $faker) {
    $email->notice = true;
});

$factory->afterMakingState(App\UserEmail::class, 'not_noticed', function ($email, $faker) {
    $email->notice = false;
});

$factory->afterMakingState(App\UserEmail::class, 'rescued', function ($email, $faker) {
    $email->rescue = true;
});

$factory->afterMakingState(App\UserEmail::class, 'not_rescued', function ($email, $faker) {
    $email->rescue = false;
});

$factory->afterMakingState(App\UserEmail::class, 'created_before_move_to_new_engine', function ($email, $faker) {
    $email->created_at = Carbon::parse($email->getMoveToNewEngineDate())->subMonth();
});

$factory->afterMakingState(App\UserEmail::class, 'show_in_profile', function ($email, $faker) {
    $email->show_in_profile = true;
    $email->confirm = true;
});

$factory->afterMakingState(App\UserEmail::class, 'dont_show_in_profile', function ($email, $faker) {
    $email->show_in_profile = false;
});