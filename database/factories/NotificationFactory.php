<?php

use Faker\Generator as Faker;

$factory->define(App\DatabaseNotification::class, function (Faker $faker) {
	return [
		'id' => $faker->uuid,
		'type' => 'App\Notifications\NewCommentReplyNotification',
		'notifiable_type' => 'user',
		'notifiable_id' => function () {
			return factory(App\User::class)->create()->id;
		},
		'data' => '{"title":"\u041e\u0431\u0440\u0430\u0431\u043e\u0442\u043a\u0430 \u043a\u043d\u0438\u0433\u0438 \u0412\u043b\u0430\u0434\u044b\u0447\u0438\u0446\u0430 \u043e\u0437\u0435\u0440\u0430 \u0437\u0430\u0432\u0435\u0440\u0448\u0438\u043b\u0430\u0441\u044c","url":"http:\/\/dev.litlife.club\/books\/316683"}',
		'read_at' => null,
		'created_at' => now(),
		'updated_at' => now()
	];
});