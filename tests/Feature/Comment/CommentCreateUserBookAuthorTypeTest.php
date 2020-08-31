<?php

namespace Tests\Feature\Comment;

use App\Author;
use App\Book;
use App\Comment;
use App\Enums\AuthorEnum;
use Tests\TestCase;

class CommentCreateUserBookAuthorTypeTest extends TestCase
{
	public function testTransaledBook()
	{
		$author = factory(Author::class)
			->states(['with_author_manager', 'with_translated_book'])
			->create()->fresh();

		$manager = $author->managers()->get()->first();
		$this->assertNotNull($manager);

		$user = $manager->user;
		$this->assertNotNull($manager);

		$book = $author->any_books()->get()->first();
		$this->assertNotNull($book);

		$comment = factory(Comment::class)
			->create([
				'create_user_id' => $user->id,
				'commentable_id' => $book->id
			]);

		$this->assertTrue($comment->isCreateUserAuthorOfBook());
		$this->assertEquals([$author->id], $book->getAuthorsManagerAssociatedWithUser($user)->pluck('id')->toArray());
		$this->assertEquals('Translator', AuthorEnum::getKey($comment->getCreateUserBookAuthor()->pivot->type));
	}

	public function testCompiledBook()
	{
		$author = factory(Author::class)
			->states(['with_author_manager', 'with_compiled_book'])
			->create()->fresh();

		$manager = $author->managers()->get()->first();
		$this->assertNotNull($manager);

		$user = $manager->user;
		$this->assertNotNull($manager);

		$book = $author->any_books()->get()->first();
		$this->assertNotNull($book);

		$comment = factory(Comment::class)
			->create([
				'create_user_id' => $user->id,
				'commentable_id' => $book->id
			]);

		$this->assertTrue($comment->isCreateUserAuthorOfBook());
		$this->assertEquals([$author->id], $book->getAuthorsManagerAssociatedWithUser($user)->pluck('id')->toArray());
		$this->assertEquals('Compiler', AuthorEnum::getKey($comment->getCreateUserBookAuthor()->pivot->type));
	}

	public function testIllustratedBook()
	{
		$author = factory(Author::class)
			->states(['with_author_manager', 'with_illustrated_book'])
			->create()->fresh();

		$manager = $author->managers()->get()->first();
		$this->assertNotNull($manager);

		$user = $manager->user;
		$this->assertNotNull($manager);

		$book = $author->any_books()->get()->first();
		$this->assertNotNull($book);

		$comment = factory(Comment::class)
			->create([
				'create_user_id' => $user->id,
				'commentable_id' => $book->id
			]);

		$this->assertTrue($comment->isCreateUserAuthorOfBook());
		$this->assertEquals([$author->id], $book->getAuthorsManagerAssociatedWithUser($user)->pluck('id')->toArray());
		$this->assertEquals('Illustrator', AuthorEnum::getKey($comment->getCreateUserBookAuthor()->pivot->type));
	}

	public function testNull()
	{
		$author = factory(Author::class)
			->states(['with_author_manager', 'with_illustrated_book'])
			->create()->fresh();

		$manager = $author->managers()->get()->first();
		$this->assertNotNull($manager);

		$user = $manager->user;
		$this->assertNotNull($manager);

		$book = $author->any_books()->get()->first();
		$this->assertNotNull($book);

		$comment = factory(Comment::class)
			->create([
				'commentable_id' => $book->id
			]);

		$this->assertFalse($comment->isCreateUserAuthorOfBook());
		$this->assertEquals([], $book->getAuthorsManagerAssociatedWithUser($comment->create_user)->pluck('id')->toArray());
		$this->assertNull($comment->getCreateUserBookAuthor());
	}

	public function testWritedTransatedCompiledIllustratedBook()
	{
		$author = factory(Author::class)
			->states(['with_author_manager', 'with_book'])
			->create()->fresh();

		$manager = $author->managers()->get()->first();
		$this->assertNotNull($manager);

		$user = $manager->user;
		$this->assertNotNull($manager);

		$book = $author->any_books()->get()->first();
		$this->assertNotNull($book);

		$book->translators()->attach([$author->id]);
		$book->compilers()->attach([$author->id]);
		$book->illustrators()->attach([$author->id]);
		$book->push();

		$this->assertEquals(4, $book->authors()->count());

		$comment = factory(Comment::class)
			->create([
				'create_user_id' => $user->id,
				'commentable_id' => $book->id
			]);

		$this->assertTrue($comment->isCreateUserAuthorOfBook());

		$this->assertEquals([$book->writers()->first()->id, $book->translators()->first()->id, $book->illustrators()->first()->id, $book->compilers()->first()->id],
			$book->getAuthorsManagerAssociatedWithUser($user)->pluck('id')->toArray());

		$this->assertEquals('Writer', AuthorEnum::getKey($comment->getCreateUserBookAuthor()->pivot->type));
	}

	public function testBookShowIsOkWithoutAuthorsWithComments()
	{
		$book = factory(Book::class)
			->states('without_any_authors')
			->create();

		$comment = factory(Comment::class)
			->states('book')
			->create(['commentable_id' => $book->id]);

		$this->get(route('books.show', $book))
			->assertOk();
	}
}
