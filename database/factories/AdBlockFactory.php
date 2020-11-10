<?php

/** @var Factory $factory */

use App\AdBlock;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

$factory->define(AdBlock::class, function (Faker $faker) {
    return [
        'name' => uniqid(),
        'code' => '<script type="text/javascript">alert("test");</script>',
        'description' => $faker->realText(100),
        'enabled' => false
    ];
});

$factory->afterMakingState(App\AdBlock::class, 'enabled', function ($adBlock, $faker) {
    $adBlock->enable();
});