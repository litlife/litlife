<?php

use Faker\Generator as Faker;

$factory->define(App\Page::class, function (Faker $faker) {

	$content = '<p>' . $faker->realText(200) . '</p>';

	return [
		'content' => $content,
		'page' => rand(1, 100),
		'section_id' => rand(1, 100),
		'book_id' => rand(1, 100),
		'character_count' => mb_strlen($content)
	];
});