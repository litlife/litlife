<?php

namespace Tests\Feature\Message;

use App\User;
use Faker\Factory as Faker;
use Tests\TestCase;

class MessageStoreTest extends TestCase
{
	public function testStoreHttp()
	{
		$recepient = factory(User::class)->create()->fresh();

		$sender = factory(User::class)->create()->fresh();

		$text = Faker::create()->realText(200);

		$this->actingAs($sender)
			->post(route('users.messages.store', ['user' => $recepient]),
				['bb_text' => $text])
			->assertSessionHasNoErrors()
			->assertRedirect(route('users.messages.index', ['user' => $recepient]));

		$message = $sender->messages()->first();

		$this->assertEquals($message->text, $text);
	}
}
