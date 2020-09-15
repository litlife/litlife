<?php

namespace Tests\Feature\Comment;

use App\Author;
use App\Comment;
use Tests\TestCase;

class CommentIsCreateUserAuthorOfBookTest extends TestCase
{
	public function testCreateUserAuthorOfBook()
	{
		$author = factory(Author::class)
			->states(['with_author_manager', 'with_book'])
			->create()->fresh();

		$manager = $author->managers()->get()->first();
		$this->assertNotNull($manager);

		$user = $manager->user;
		$this->assertNotNull($manager);

		$book = $author->books()->get()->first();
		$this->assertNotNull($book);

		$comment = factory(Comment::class)
			->create([
				'create_user_id' => $user->id,
				'commentable_id' => $book->id
			]);

		$this->assertTrue($comment->isCreateUserAuthorOfBook());
	}

	public function testCreateUserNotAuthorOfBook()
	{
		$author = factory(Author::class)
			->states(['with_author_manager', 'with_book'])
			->create()->fresh();

		$book = $author->books()->get()->first();
		$this->assertNotNull($book);

		$comment = factory(Comment::class)
			->create([
				'commentable_id' => $book->id
			]);

		$this->assertFalse($comment->isCreateUserAuthorOfBook());
	}

	public function testCreateUserAuthorOnReviewOfBook()
	{
		$author = factory(Author::class)
			->states(['with_author_manager_on_review', 'with_book'])
			->create()->fresh();

		$manager = $author->managers()->get()->first();
		$this->assertNotNull($manager);

		$user = $manager->user;
		$this->assertNotNull($manager);

		$book = $author->books()->get()->first();
		$this->assertNotNull($book);

		$comment = factory(Comment::class)
			->create([
				'create_user_id' => $user->id,
				'commentable_id' => $book->id
			]);

		$this->assertFalse($comment->isCreateUserAuthorOfBook());
	}

	public function testCreateUserEditorOfBook()
	{
		$author = factory(Author::class)
			->states(['with_editor_manager', 'with_book'])
			->create()->fresh();

		$manager = $author->managers()->get()->first();
		$this->assertNotNull($manager);

		$user = $manager->user;
		$this->assertNotNull($manager);

		$book = $author->books()->get()->first();
		$this->assertNotNull($book);

		$comment = factory(Comment::class)
			->create([
				'create_user_id' => $user->id,
				'commentable_id' => $book->id
			]);

		$this->assertFalse($comment->isCreateUserAuthorOfBook());
	}

}
