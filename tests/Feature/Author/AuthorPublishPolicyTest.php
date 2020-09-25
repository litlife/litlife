<?php

namespace Tests\Feature\Author;

use App\Author;
use App\User;
use Tests\TestCase;

class AuthorPublishPolicyTest extends TestCase
{
	public function testCanIfHasPerimission()
	{
		$user = factory(User::class)->create();

		$author = factory(Author::class)
			->states('sent_for_review')
			->create();

		$user->group->check_books = true;
		$user->push();

		$this->assertTrue($user->can('makeAccepted', $author));
	}

	public function testCantIfDoesntHavePerimission()
	{
		$user = factory(User::class)->create();

		$author = factory(Author::class)
			->states('sent_for_review')
			->create();

		$user->group->check_books = false;
		$user->push();

		$this->assertFalse($user->can('makeAccepted', $author));
	}

	public function testCantIfAuthorNotSentForReview()
	{
		$user = factory(User::class)
			->create();

		$author = factory(Author::class)
			->create();

		$user->group->check_books = true;
		$user->push();

		$this->assertFalse($user->can('makeAccepted', $author));
	}
}
