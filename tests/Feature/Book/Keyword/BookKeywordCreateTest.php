<?php

namespace Tests\Feature\Book\Keyword;

use App\Book;
use App\Enums\StatusEnum;
use App\Keyword;
use App\User;
use Illuminate\Support\Str;
use Tests\TestCase;

class BookKeywordCreateTest extends TestCase
{
	public function testIfKeywordExists()
	{
		$book = factory(Book::class)
			->create();
		$book->statusAccepted();
		$book->save();

		$user = factory(User::class)->create();
		$user->group->book_keyword_add = true;
		$user->push();

		$keyword = factory(Keyword::class)->create();
		$keyword->statusAccepted();
		$keyword->save();

		$response = $this->actingAs($user)
			->post(route('books.keywords.store', ['book' => $book]),
				['keywords' => [$keyword->text]])
			->assertRedirect();

		$book_keyword = $book->book_keywords()->first();

		$this->assertEquals(StatusEnum::Accepted, $book_keyword->status);
		$this->assertEquals(StatusEnum::Accepted, $book_keyword->keyword->status);
	}

	public function testCanAttachExisted()
	{
		$book = factory(Book::class)->create();
		$book->statusAccepted();
		$book->save();

		$user = factory(User::class)->create();
		$user->group->book_keyword_add = true;
		$user->group->book_keyword_add_new_with_check = false;
		$user->group->book_keyword_moderate = false;
		$user->push();

		$keyword = factory(Keyword::class)->create();
		$keyword->statusAccepted();
		$keyword->save();

		$response = $this->actingAs($user)
			->post(route('books.keywords.store', ['book' => $book]),
				['keywords' => [$keyword->text]])
			->assertRedirect();

		$book_keyword = $book->book_keywords()->first();

		$this->assertEquals(StatusEnum::Accepted, $book_keyword->status);
		$this->assertEquals(StatusEnum::Accepted, $book_keyword->keyword->status);
	}

	public function testCanAttachById()
	{
		$book = factory(Book::class)->create();
		$book->statusAccepted();
		$book->save();

		$user = factory(User::class)->create();
		$user->group->book_keyword_add = true;
		$user->group->book_keyword_add_new_with_check = false;
		$user->group->book_keyword_moderate = false;
		$user->push();

		$keyword = factory(Keyword::class)->create();
		$keyword->statusAccepted();
		$keyword->save();

		$response = $this->actingAs($user)
			->post(route('books.keywords.store', ['book' => $book]),
				['keywords' => [$keyword->id]])
			->assertRedirect();

		$book_keyword = $book->book_keywords()->first();

		$this->assertEquals($keyword->id, $book_keyword->keyword->id);
		$this->assertEquals(StatusEnum::Accepted, $book_keyword->status);
		$this->assertEquals(StatusEnum::Accepted, $book_keyword->keyword->status);
	}

	public function testCantAttachNew()
	{
		$book = factory(Book::class)->create();
		$book->statusAccepted();
		$book->save();

		$user = factory(User::class)->create();
		$user->group->book_keyword_add = true;
		$user->group->book_keyword_add_new_with_check = false;
		$user->group->book_keyword_moderate = false;
		$user->push();

		$text = Str::random(8);

		$response = $this->actingAs($user)
			->post(route('books.keywords.store', ['book' => $book]),
				['keywords' => [$text]])
			->assertRedirect();

		$this->assertEquals(0, $book->book_keywords()->count());
	}

	public function testCanAttachNewOnCheck()
	{
		$book = factory(Book::class)->create();
		$book->statusAccepted();
		$book->save();

		$user = factory(User::class)->create();
		$user->group->book_keyword_add = true;
		$user->group->book_keyword_add_new_with_check = true;
		$user->group->book_keyword_moderate = false;
		$user->push();

		$text = Str::random(10);

		$response = $this->actingAs($user)
			->post(route('books.keywords.store', ['book' => $book]),
				['keywords' => [$text]])
			->assertRedirect();

		$book_keyword = $book->book_keywords()->first();

		$this->assertEquals(mb_ucfirst($text), $book_keyword->keyword->text);
		$this->assertTrue($book_keyword->isSentForReview());
		$this->assertTrue($book_keyword->keyword->isSentForReview());
	}

	public function testCanAttachNewAccepted()
	{
		$book = factory(Book::class)->create();
		$book->statusAccepted();
		$book->save();

		$user = factory(User::class)->create();
		$user->group->book_keyword_add = true;
		$user->group->book_keyword_add_new_with_check = true;
		$user->group->book_keyword_moderate = true;
		$user->push();

		$text = Str::random(8);

		$response = $this->actingAs($user)
			->post(route('books.keywords.store', ['book' => $book]),
				['keywords' => [$text]])
			->assertRedirect();

		$book_keyword = $book->book_keywords()->first();

		$this->assertEquals(mb_ucfirst($text), $book_keyword->keyword->text);
		$this->assertTrue($book_keyword->isAccepted());
		$this->assertTrue($book_keyword->keyword->isAccepted());
		$this->assertEquals($book_keyword->book_id, $book_keyword->origin_book_id);
	}

	public function testCanAttachNewAccepted2()
	{
		$book = factory(Book::class)->create();
		$book->statusAccepted();
		$book->save();

		$user = factory(User::class)->create();
		$user->group->book_keyword_add = false;
		$user->group->book_keyword_add_new_with_check = false;
		$user->group->book_keyword_moderate = true;
		$user->push();

		$text = Str::random(10);

		$response = $this->actingAs($user)
			->post(route('books.keywords.store', ['book' => $book]),
				['keywords' => [$text]])
			->assertRedirect();

		$book_keyword = $book->book_keywords()->first();

		$this->assertEquals(mb_ucfirst($text), $book_keyword->keyword->text);
		$this->assertTrue($book_keyword->isAccepted());
		$this->assertTrue($book_keyword->keyword->isAccepted());
	}

	public function testCantAttachNewToPrivateBook()
	{
		$book = factory(Book::class)
			->states('with_create_user', 'private')
			->create();

		$user = $book->create_user;
		$user->group->book_keyword_add = false;
		$user->group->book_keyword_add_new_with_check = false;
		$user->group->book_keyword_moderate = false;
		$user->push();

		$text = Str::random(7);

		$response = $this->actingAs($user)
			->post(route('books.keywords.store', ['book' => $book]),
				['keywords' => [$text]])
			->assertRedirect();

		$this->assertEquals(0, $book->book_keywords()->count());
	}

	public function testCanAttachExistedToPrivateBook()
	{
		$book = factory(Book::class)
			->states('with_create_user', 'private')
			->create();

		$user = $book->create_user;
		$user->group->book_keyword_add = false;
		$user->group->book_keyword_add_new_with_check = false;
		$user->group->book_keyword_moderate = false;
		$user->push();

		$keyword = factory(Keyword::class)->create();
		$keyword->statusAccepted();
		$keyword->save();

		$response = $this->actingAs($user)
			->post(route('books.keywords.store', ['book' => $book]),
				['keywords' => [$keyword->text]])
			->assertRedirect();

		$book_keyword = $book->book_keywords()->first();

		$this->assertTrue($book_keyword->isPrivate());
		$this->assertTrue($book_keyword->keyword->isAccepted());
	}

	public function testCantAttachExistedPrivateToPrivateBook()
	{
		$book = factory(Book::class)
			->states('with_create_user', 'private')
			->create();

		$user = $book->create_user;
		$user->group->book_keyword_add = false;
		$user->group->book_keyword_add_new_with_check = false;
		$user->group->book_keyword_moderate = false;
		$user->push();

		$keyword = factory(Keyword::class)->create();
		$keyword->statusPrivate();
		$keyword->save();

		$response = $this->actingAs($user)
			->post(route('books.keywords.store', ['book' => $book]),
				['keywords' => [$keyword->text]])
			->assertRedirect();

		$book_keyword = $book->book_keywords()->first();

		$this->assertEquals(0, $book->book_keywords()->count());
	}

	public function testAttachExistedAndNew()
	{
		$book = factory(Book::class)->create();
		$book->statusAccepted();
		$book->save();

		$user = factory(User::class)->create();
		$user->group->book_keyword_add = true;
		$user->group->book_keyword_add_new_with_check = false;
		$user->group->book_keyword_moderate = false;
		$user->push();

		$keyword = factory(Keyword::class)->create();
		$keyword->statusAccepted();
		$keyword->save();

		$new_text = Str::random(8);

		$response = $this->actingAs($user)
			->post(route('books.keywords.store', ['book' => $book]),
				['keywords' => [$new_text, $keyword->text]])
			->assertRedirect();

		$book_keyword_existed = $book->book_keywords()
			->where('keyword_id', $keyword->id)
			->first();

		$this->assertEquals(StatusEnum::Accepted, $book_keyword_existed->status);
		$this->assertEquals(StatusEnum::Accepted, $book_keyword_existed->keyword->status);

		$book_keyword_new = $book->book_keywords()
			->where('keyword_id', '!=', $keyword->id)
			->first();

		$this->assertNull($book_keyword_new);
	}

	public function testIfKeywordForceDeleted()
	{
		$book = factory(Book::class)
			->create();

		$user = factory(User::class)
			->states('admin')
			->create();

		$keyword = factory(Keyword::class)
			->create();

		$id = $keyword->id;

		$keyword->forceDelete();

		$response = $this->actingAs($user)
			->post(route('books.keywords.store', ['book' => $book]),
				['keywords' => [$id]])
			->assertSessionHasNoErrors()
			->assertRedirect();
	}
}
