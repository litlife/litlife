<?php

use Faker\Generator as Faker;

$factory->define(App\Image::class, function (Faker $faker) {

    return [
        'storage' => config('filesystems.default'),
        'create_user_id' => function () {
            return factory(App\User::class)->create()->id;
        }
    ];
});

$factory->afterMaking(App\Image::class, function ($image, $faker) {

    $imagick = new Imagick();
    $imagick->newImage(100, 100, new ImagickPixel('red'));
    $imagick->setImageFormat('png');

    $image->openImage($imagick);
});