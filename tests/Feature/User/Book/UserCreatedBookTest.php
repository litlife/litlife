<?php

namespace Tests\Feature\User\Book;

use App\User;
use Tests\TestCase;

class UserCreatedBookTest extends TestCase
{
	public function testCreatedIsOk()
	{
		$user = User::factory()->create();

		$this->actingAs($user)
			->get(route('users.books.created', ['user' => $user]))
			->assertOk();
	}
}
