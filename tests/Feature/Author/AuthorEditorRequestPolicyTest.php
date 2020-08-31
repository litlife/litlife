<?php

namespace Tests\Feature\Author;

use App\Author;
use App\User;
use Tests\TestCase;

class AuthorEditorRequestPolicyTest extends TestCase
{
	public function testFalseIfAuthorPrivate()
	{
		$user = factory(User::class)
			->states('admin')
			->create();

		$author = factory(Author::class)
			->states('private')
			->create();

		$this->assertTrue($user->can('editorRequest', $author));
	}

	public function testTrueIfAuthorHasEditorManager()
	{
		$user = factory(User::class)
			->states('admin')
			->create();

		$author = factory(Author::class)
			->states('with_editor_manager')
			->create();

		$this->assertTrue($user->can('editorRequest', $author));
	}

	public function testFalseIfEditorManagerOnReview()
	{
		$author = factory(Author::class)
			->states('with_editor_manager')
			->create();

		$manager = $author->managers()->first();
		$manager->statusSentForReview();
		$manager->save();

		$user = $manager->user;
		$user->group->author_editor_request = true;
		$user->push();

		$this->assertFalse($user->can('editorRequest', $author));
	}

	public function testTrueIfOtherUserEditorManagerOnReview()
	{
		$author = factory(Author::class)
			->states('with_editor_manager')
			->create();

		$manager = $author->managers()->first();
		$manager->statusSentForReview();
		$manager->save();

		$user = factory(User::class)
			->states('admin')
			->create();

		$this->assertTrue($user->can('editorRequest', $author));
	}

	public function testFalseIfUserRequestVerification()
	{
		$author = factory(Author::class)
			->states('with_author_manager_on_review')
			->create();

		$manager = $author->managers()->first();
		$user = $manager->user;
		$user->group->author_editor_request = true;
		$user->push();

		$this->assertFalse($user->can('editorRequest', $author));
	}

	public function testFalseIfAuthorManagerPrivate()
	{
		$author = factory(Author::class)
			->states('private', 'with_author_manager')
			->create();

		$manager = $author->managers()->first();
		$manager->statusPrivate();
		$manager->save();

		$user = $manager->user;
		$user->group->author_editor_request = true;
		$user->push();

		$this->assertFalse($user->can('editorRequest', $author));
	}

	public function testFalseIfUserAuthor()
	{
		$author = factory(Author::class)
			->states('accepted', 'with_author_manager')
			->create();

		$manager = $author->managers()->first();
		$manager->statusAccepted();
		$manager->save();

		$user = $manager->user;
		$user->group->author_editor_request = true;
		$user->push();

		$this->assertFalse($user->can('editorRequest', $author));
	}

	public function testTrueIfUserNotAuthor()
	{
		$author = factory(Author::class)
			->states('accepted', 'with_author_manager')
			->create();

		$manager = $author->managers()->first();
		$manager->statusAccepted();
		$manager->save();

		$user = factory(User::class)->create();
		$user->group->author_editor_request = true;
		$user->push();

		$this->assertTrue($user->can('editorRequest', $author));
	}
}
