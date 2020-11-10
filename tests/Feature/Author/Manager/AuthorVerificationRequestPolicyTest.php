<?php

namespace Tests\Feature\Author\Manager;

use App\Author;
use App\User;
use Tests\TestCase;

class AuthorVerificationRequestPolicyTest extends TestCase
{
	public function testFalseIfAuthorPrivate()
	{
		$user = User::factory()->admin()->create();

		$author = Author::factory()->private()->create();

		$this->assertTrue($user->can('verficationRequest', $author));
	}

	public function testFalseIfAuthorHasAuthorManager()
	{
		$user = User::factory()->admin()->create();

		$author = Author::factory()->with_author_manager()->create();

		$this->assertFalse($user->can('verficationRequest', $author));
	}

	public function testTrueIfAuthorAccepted()
	{
		$user = User::factory()->admin()->create();

		$author = Author::factory()->accepted()->create();

		$this->assertTrue($user->can('verficationRequest', $author));
	}

	public function testFalseIfNotPermissions()
	{
		$user = User::factory()->create();
		$user->group->author_editor_request = false;
		$user->push();

		$author = Author::factory()->accepted()->create();

		$this->assertFalse($user->can('verficationRequest', $author));
	}

	public function testFalseIfRequestOnReview()
	{
		$author = Author::factory()->with_author_manager_on_review()->create();

		$manager = $author->managers()->first();
		$user = $manager->user;
		$user->group->author_editor_request = true;
		$user->push();

		$this->assertFalse($user->can('verficationRequest', $author));
	}

	public function testTrueIfOtherUserRequestOnReview()
	{
		$author = Author::factory()->with_author_manager_on_review()->create();

		$manager = $author->managers()->first();
		$user = $manager->user;
		$user->group->author_editor_request = true;
		$user->push();

		$user = User::factory()->admin()->create();

		$this->assertTrue($user->can('verficationRequest', $author));
	}
}
