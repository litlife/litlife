<?php

namespace Tests\Feature\Book;

use App\Book;
use App\User;
use Tests\TestCase;

class BookEditFieldOfPublicDomainPolicyTest extends TestCase
{
	public function testEditFieldOfPublicDomainPolicy()
	{
		$user = User::factory()->create();

		$book = Book::factory()->create();

		$this->assertFalse($user->can('editFieldOfPublicDomain', $book));

		$user->group->edit_field_of_public_domain = true;
		$user->push();

		$this->assertTrue($user->can('editFieldOfPublicDomain', $book));
	}
}
