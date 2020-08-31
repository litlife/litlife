<?php

namespace Tests\Feature\Author;

use App\Author;
use App\User;
use Tests\TestCase;

class AuthorVerificationRequestPolicyTest extends TestCase
{
	public function testFalseIfAuthorPrivate()
	{
		$user = factory(User::class)
			->states('admin')
			->create();

		$author = factory(Author::class)
			->states('private')
			->create();

		$this->assertTrue($user->can('verficationRequest', $author));
	}

	public function testFalseIfAuthorHasAuthorManager()
	{
		$user = factory(User::class)
			->states('admin')
			->create();

		$author = factory(Author::class)
			->states('with_author_manager')
			->create();

		$this->assertFalse($user->can('verficationRequest', $author));
	}

	public function testTrueIfAuthorAccepted()
	{
		$user = factory(User::class)
			->states('admin')
			->create();

		$author = factory(Author::class)
			->states('accepted')
			->create();

		$this->assertTrue($user->can('verficationRequest', $author));
	}

	public function testFalseIfNotPermissions()
	{
		$user = factory(User::class)->create();
		$user->group->author_editor_request = false;
		$user->push();

		$author = factory(Author::class)
			->states('accepted')
			->create();

		$this->assertFalse($user->can('verficationRequest', $author));
	}

	public function testFalseIfRequestOnReview()
	{
		$author = factory(Author::class)
			->states('with_author_manager_on_review')
			->create();

		$manager = $author->managers()->first();
		$user = $manager->user;
		$user->group->author_editor_request = true;
		$user->push();

		$this->assertFalse($user->can('verficationRequest', $author));
	}

	public function testTrueIfOtherUserRequestOnReview()
	{
		$author = factory(Author::class)
			->states('with_author_manager_on_review')
			->create();

		$manager = $author->managers()->first();
		$user = $manager->user;
		$user->group->author_editor_request = true;
		$user->push();

		$user = factory(User::class)
			->states('admin')
			->create();

		$this->assertTrue($user->can('verficationRequest', $author));
	}
}
