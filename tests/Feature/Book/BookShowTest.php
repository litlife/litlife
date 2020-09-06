<?php

namespace Tests\Feature\Book;

use App\Author;
use App\Book;
use App\BookFile;
use App\Jobs\Book\UpdateBookFilesCount;
use App\User;
use App\UserPurchase;
use Tests\TestCase;

class BookShowTest extends TestCase
{
	public function testViewBookIfUserBuyThisBook()
	{
		$book = factory(Book::class)
			->create();

		$book->delete();

		$reader = factory(User::class)
			->create();

		$purchase = factory(UserPurchase::class)
			->create([
				'buyer_user_id' => $reader->id,
				'purchasable_type' => 'book',
				'purchasable_id' => $book->id,
			]);

		$this->actingAs($reader)
			->get(route('books.show', $book))
			->assertOk()
			->assertDontSeeText($book->title)
			->assertSeeText(__('Book was deleted'));
	}

	public function testSeeTextRemoveFromSaleHttp()
	{
		$book = factory(Book::class)
			->states('removed_from_sale')
			->create();

		$user = factory(User::class)
			->create();

		$this->actingAs($user)
			->get(route('books.show', ['book' => $book]))
			->assertOk()
			->assertSeeText(__('book.removed_from_sale'));
	}

	public function testSeePrivateAuthorIfBookSentOnReview()
	{
		$book = factory(Book::class)
			->states('sent_for_review')
			->create();

		$author = factory(Author::class)
			->states('private')
			->create();

		$book->authors()->sync([$author->id]);

		$user = $author->create_user;

		$this->actingAs($user)
			->get(route('books.show', $book))
			->assertOk()
			->assertSeeText($author->name);

		$other_user = factory(User::class)->create();

		$this->actingAs($other_user)
			->get(route('books.show', $book))
			->assertOk()
			->assertDontSeeText($author->name);
	}

	public function testShowSentForReviewOk()
	{
		$user = factory(User::class)->create();

		$book = factory(Book::class)
			->states('sent_for_review')
			->create();
		$book->status_changed_user_id = $user->id;
		$book->save();

		$this->assertTrue($book->isSentForReview());

		$this->get(route('books.show', $book))
			->assertOk();
	}

	public function testShowPrivateBook()
	{
		$book = factory(Book::class)
			->states('private', 'with_create_user')
			->create();

		$this->get(route('books.show', $book))
			->assertForbidden()
			->assertSeeText(__('book.access_denied'));
	}

	public function testShowSentForReviewBook()
	{
		$book = factory(Book::class)
			->states('sent_for_review')
			->create();

		$this->get(route('books.show', $book))
			->assertOk()
			->assertSeeText(__('book.on_check'));
	}

	public function testGuestSeeOnReview()
	{
		$book = factory(Book::class)
			->states('sent_for_review')
			->create();

		$response = $this->get(route('books.show', $book))
			->assertOk()
			->assertSeeText($book->title)
			->assertSeeText(__('book.on_check'))
			->assertDontSeeText(__('book.you_will_receive_a_notification_when_the_book_is_published'))
			->assertDontSeeText(__('book.added_for_check'));
	}

	public function testViewFilesOnReviewIfBookOnReview()
	{
		foreach (BookFile::sentOnReview()->get() as $file)
			$file->delete();

		$book = factory(Book::class)->create();
		$book->statusSentForReview();
		$book->save();
		$book->refresh();

		$book_file = factory(BookFile::class)->states('txt')->create(['book_id' => $book->id]);
		$book_file->statusSentForReview();
		$book_file->save();
		UpdateBookFilesCount::dispatch($book);
		$book->refresh();

		$this->get(route('books.show', $book))
			->assertOk()
			->assertDontSeeText($book_file->extension);

		$admin = factory(User::class)->states('with_user_group')->create();
		$admin->group->book_file_add_check = true;
		$admin->push();

		$user = factory(User::class)->create();

		$this->actingAs($admin)
			->get(route('books.show', $book))
			->assertOk()
			->assertSeeText($book_file->extension);

		$this->actingAs($user)
			->get(route('books.show', $book))
			->assertOk()
			->assertDontSeeText($book_file->extension);
	}

}
