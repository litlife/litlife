<?php

use Faker\Generator as Faker;

$factory->define(App\BookFileDownloadLog::class, function (Faker $faker) {
	return [
		'book_file_id' => function () {
			return factory(App\BookFile::class)->states('txt')->create()->id;
		},
		'user_id' => function () {
			return factory(App\User::class)->create()->id;
		},
		'ip' => $faker->ipv4
	];
});

