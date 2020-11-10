<?php

use App\UserGroup;
use Faker\Generator as Faker;

$factory->define(App\UserGroup::class, function (Faker $faker) {

    //dd(UserGroup);

    return [
        'name' => $faker->realText(30),
        'created_at' => now(),
        'updated_at' => now(),
    ];
});
/*
$factory->state(App\UserGroup::class, 'administrators', function ($faker) {

    return [
        'name' => 'administrators',
    ];
});

$factory->afterMakingState(App\UserGroup::class, 'administrators', function ($group, $faker) {
    foreach ($group->permissions as $permission) {
        $group->{$permission} = true;
    }
});
*/

$factory->afterMakingState(App\UserGroup::class, 'administrator', function ($group, $faker) {
    foreach ($group->permissions as $name => $value) {
        $group->{$name} = true;
    }
    $group->save();
});

$factory->afterMakingState(App\UserGroup::class, 'user', function ($group, $faker) {
    $group->send_message = true;
    $group->blog = true;
    $group->add_forum_post = true;
    $group->shop_enable = true;
    $group->manage_collections = true;
});

$factory->afterMakingState(App\UserGroup::class, 'notify_assignment', function (UserGroup $group, $faker) {
    $group->notify_assignment = true;
});

$factory->afterMakingState(App\UserGroup::class, 'notify_assignment_disable', function (UserGroup $group, $faker) {
    $group->notify_assignment = false;
});