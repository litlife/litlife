<?php

namespace Tests\Feature\User\Book;

use App\User;
use Tests\TestCase;

class UserBookTest extends TestCase
{
	public function testCreatedIsOk()
	{
		$user = factory(User::class)->create();

		$this->actingAs($user)
			->get(route('users.books.created', ['user' => $user]))
			->assertOk();
	}
}
