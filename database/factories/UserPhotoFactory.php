<?php

use App\UserPhoto;
use Faker\Generator as Faker;

$factory->define(App\UserPhoto::class, function (Faker $faker) {

	return [
		'user_id' => function () {
			return factory(App\User::class)->create()->id;
		},
	];
});

$factory->afterMaking(App\UserPhoto::class, function (UserPhoto $photo, $faker) {

	$image = new Imagick();
	$image->newImage(300, 300, new ImagickPixel('red'));
	$image->setImageFormat('jpeg');

	$photo->openImage($image);
});