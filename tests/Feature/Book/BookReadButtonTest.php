<?php

namespace Tests\Feature\Book;

use App\Author;
use App\Book;
use App\Jobs\Book\UpdateBookPagesCount;
use App\Section;
use App\User;
use Tests\TestCase;

class BookReadButtonTest extends TestCase
{
	public function testSeeReadButtonText()
	{
		$book = Book::factory()->with_three_sections()->create();
		$book->price = 100;
		$book->free_sections_count = 1;
		$book->save();

		$this->assertEquals(3, $book->sections_count);

		$response = $this->get(route('books.show', ['book' => $book]))
			->assertOk()
			->assertSeeText(__('common.read_online'));
		//$response->assertSeeText(trans_choice('book.buy_a_book', $book->price, ['price' => $book->price]));

		$book->price = 100;
		$book->free_sections_count = 0;
		$book->push();

		$response = $this->get(route('books.show', ['book' => $book]))
			->assertOk()
			->assertSeeText(__('common.read_online'));
		//$response->assertSeeText(trans_choice('book.buy_a_book', $book->price, ['price' => $book->price]));

		$book->price = 0;
		$book->free_sections_count = 1;
		$book->push();

		$response = $this->get(route('books.show', ['book' => $book]))
			->assertOk()
			->assertSeeText(__('common.read_online'));
		//$response->assertDontSeeText(trans_choice('book.buy_a_book', $book->price, ['price' => $book->price]));

		$book->price = 100;
		$book->free_sections_count = 0;
		$book->push();
	}

	public function testSeeReadButtonTextIfBookForSellAndUserHaveAccessToClosedBooks()
	{
		$book = Book::factory()->with_three_sections()->complete()->create();
		$book->price = 100;
		$book->free_sections_count = 0;
		$book->is_si = true;
		$book->is_lp = false;
		$book->save();

		$admin = User::factory()->create();
		$admin->group->access_to_closed_books = true;
		$admin->push();

		$this->actingAs($admin)
			->get(route('books.show', ['book' => $book]))
			->assertOk()
			->assertSeeText(__('common.read_online'))
			->assertSeeText(trans_choice('book.buy_a_book', $book->price, ['price' => $book->price]));
	}

	public function testSeeReadButtonTextIfReadAccessDisabledAndUserHaveAccessToClosedBooks()
	{
		$book = Book::factory()->with_three_sections()->create();
		$book->readAccessDisable();
		$book->save();

		$admin = User::factory()->create();
		$admin->group->access_to_closed_books = true;
		$admin->push();

		$this->actingAs($admin)
			->get(route('books.show', ['book' => $book]))
			->assertOk()
			->assertSeeText(__('common.read_online'))
			->assertDontSeeText(trans_choice('book.buy_a_book', $book->price, ['price' => $book->price]))
			->assertDontSeeText(__('book.message_that_there_is_no_access'));
	}

	public function testSeeReadButtonTextIfAuthorOfBook()
	{
		$author = Author::factory()->with_author_manager_can_sell()->with_book()->create();

		$user = $author->managers->first()->user;
		$book = $author->books->first();
		$book->create_user()->associate($user);
		$book->is_si = true;
		$book->is_lp = false;
		$book->push();

		$this->assertTrue($user->can('sell', $book));

		$this->actingAs($user)
			->get(route('books.show', ['book' => $book]))
			->assertOk()
			->assertSeeText(__('common.read_online'))
			->assertDontSeeText(trans_choice('book.buy_a_book', $book->price, ['price' => $book->price]))
			->assertDontSeeText(__('book.message_that_there_is_no_access'));

		$book->price = 100;
		$book->free_sections_count = 0;
		$book->push();

		$this->actingAs($user)
			->get(route('books.show', ['book' => $book]))
			->assertOk()
			->assertSeeText(__('common.read_online'))
			//->assertSeeText(trans_choice('book.buy_a_book', $book->price, ['price' => $book->price]))
			->assertDontSeeText(__('book.message_that_there_is_no_access'));

		$book->readAccessDisable();
		$book->save();

		$this->actingAs($user)
			->get(route('books.show', ['book' => $book]))
			->assertOk()
			->assertDontSeeText(__('common.read_online'))
			->assertDontSeeText(trans_choice('book.buy_a_book', $book->price, ['price' => $book->price]))
			->assertDontSeeText(__('book.message_that_there_is_no_access'));
	}

	public function testDontSeeReadButtonIfReadAccessDisabled()
	{
		$book = Book::factory()->with_three_sections()->create();
		$book->readAccessDisable();
		$book->downloadAccessDisable();
		$book->save();

		$user = User::factory()->create();

		$this->assertFalse($book->isReadOrDownloadAccess());

		$this->actingAs($user)
			->get(route('books.show', ['book' => $book]))
			->assertOk()
			->assertDontSeeText(__('common.read_online'))
			->assertDontSeeText(trans_choice('book.buy_a_book', $book->price, ['price' => $book->price]))
			->assertSeeText(__('book.message_that_there_is_no_access'));
	}

	public function testSeeIfBookForSaleAndPrivate()
	{
		$author = Author::factory()->with_author_manager_can_sell()->with_private_book()->create();

		$user = $author->managers->first()->user;
		$book = $author->books->first();

		$book->create_user()->associate($user);
		$book->price = 100;
		$book->free_sections_count = 0;
		$book->push();

		$this->assertTrue($book->isPrivate());

		$this->actingAs($user)
			->get(route('books.show', ['book' => $book]))
			->assertOk()
			->assertSeeText(__('common.read_online'))
			//->assertSeeText(trans_choice('book.buy_a_book', $book->price, ['price' => $book->price]))
			->assertSeeText(__('book.for_the_book_to_start_being_sold_you_must_publish_it'));
	}

	public function testDontShowIfBookForSaleAndClosed()
	{
		$book = Book::factory()->with_three_sections()->create();
		$book->readAccessDisable();
		$book->downloadAccessDisable();
		$book->price = 100;
		$book->free_sections_count = 1;
		$book->save();

		$user = User::factory()->create();

		$this->assertFalse($book->isReadOrDownloadAccess());

		$this->actingAs($user)
			->get(route('books.show', ['book' => $book]))
			->assertOk()
			->assertDontSeeText(__('common.read_online'))
			->assertDontSeeText(trans_choice('book.buy_a_book', $book->price, ['price' => $book->price]))
			->assertSeeText(__('book.message_that_there_is_no_access'));
	}

	public function testDontShowBuyButtonIfAlreadyBuy()
	{
		$user = User::factory()->with_purchased_book()->create();

		$book = $user->purchases->first()->purchasable;
		$book->price = 100;
		$book->push();

		$this->actingAs($user)
			->get(route('books.show', ['book' => $book]))
			->assertOk()
			->assertSeeText(__('common.read_online'))
			->assertDontSeeText(trans_choice('book.buy_a_book', $book->price, ['price' => $book->price]));
	}

	public function testDontShowBookForSaleIfShopDisable()
	{
		$book = Book::factory()->with_three_sections()->create();
		$book->price = 100;
		$book->free_sections_count = 1;
		$book->save();

		$user = User::factory()->create();
		$user->group->shop_enable = false;
		$user->push();

		$this->actingAs($user)
			->get(route('books.show', ['book' => $book]))
			->assertOk()
			->assertDontSeeText(__('common.read_online'))
			->assertDontSeeText(trans_choice('book.buy_a_book', $book->price, ['price' => $book->price]));
	}

	public function testDontShowIfSectionPrivate()
	{
		$book = Book::factory()->create();

		$section = Section::factory()->private()->create();

		UpdateBookPagesCount::dispatch($book);

		$this->get(route('books.show', ['book' => $book]))
			->assertOk()
			->assertDontSeeText(__('common.read_online'))
			->assertViewHas(['first_section' => null]);
	}
}
