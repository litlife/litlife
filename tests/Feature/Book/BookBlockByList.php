<?php

namespace Tests\Feature\Book;

use App\Book;
use App\User;
use Illuminate\Support\Str;
use Tests\TestCase;

class BookBlockByList extends TestCase
{
	public function testEnterBlockingListIsOk()
	{
		$admin = User::factory()->admin()->create();

		$this->actingAs($admin)
			->get(route('books.access_by_list.enter'))
			->assertOk();
	}

	public function testDisableAccessIsLogExists()
	{
		config(['activitylog.enabled' => true]);

		$admin = User::factory()->admin()->create();

		$book = Book::factory()->create();

		$book2 = Book::factory()->create();

		$this->assertTrue($book->isReadAccess());
		$this->assertTrue($book->isDownloadAccess());

		$text = $this->faker->realText(500)
			. ' ' . route('books.show', $book) . ' ' .
			$this->faker->realText(500)
			. ' /books/' . $book->id . ' ' .
			$this->faker->realText(500);

		$reasonForChangingAccess = $this->faker->realText(100);

		$this->actingAs($admin)
			->post(route('books.access_by_list.disable'),
				[
					'text' => $text,
					'reason_for_changing_access' => $reasonForChangingAccess
				])
			->assertSessionHasNoErrors()
			->assertRedirect();

		$book_ids = session()->get('book_ids');

		$this->assertContains($book->id, $book_ids);
		$this->assertNotContains($book2->id, $book_ids);

		$book->refresh();

		$this->assertFalse($book->isReadAccess());
		$this->assertFalse($book->isDownloadAccess());
		$this->assertEquals($reasonForChangingAccess, $book->secret_hide_reason);

		$book2->refresh();

		$this->assertTrue($book2->isReadAccess());
		$this->assertTrue($book2->isDownloadAccess());

		$activity = $book->activities()->first();

		$this->assertEquals(1, $book->activities()->count());
		$this->assertEquals('change_access', $activity->description);
		$this->assertEquals($admin->id, $activity->causer_id);
		$this->assertEquals('user', $activity->causer_type);
	}

	public function testSeeBlockedBooksList()
	{
		$admin = User::factory()->admin()->create();

		$book = Book::factory()->create(['title' => Str::random(8)]);

		$this->actingAs($admin)
			->withSession([
				'book_ids' => [$book->id],
				'success' => __('book.access_to_the_specified_books_is_closed')
			])
			->get(route('books.access_by_list.enter'))
			->assertOk()
			->assertSeeText($book->title);
	}

	public function testDontDisableAccessIfBookIsOnSale()
	{
		$admin = User::factory()->admin()->create();

		$book = Book::factory()->on_sale()->create();

		$this->assertTrue($book->isReadAccess());
		$this->assertTrue($book->isDownloadAccess());

		$text = $this->faker->realText(500)
			. ' ' . route('books.show', $book) . ' ' .
			$this->faker->realText(500);

		$reasonForChangingAccess = $this->faker->realText(100);

		$this->actingAs($admin)
			->post(route('books.access_by_list.disable'),
				[
					'text' => $text,
					'reason_for_changing_access' => $reasonForChangingAccess
				])
			->assertSessionHasNoErrors()
			->assertRedirect();

		$book->refresh();

		$this->assertTrue($book->isReadAccess());
		$this->assertTrue($book->isDownloadAccess());
	}

	public function testBookLinksNotFound()
	{
		$admin = User::factory()->admin()->create();

		$text = $this->faker->realText(500);

		$reasonForChangingAccess = $this->faker->realText(100);

		$this->actingAs($admin)
			->post(route('books.access_by_list.disable'),
				[
					'text' => $text,
					'reason_for_changing_access' => $reasonForChangingAccess
				])
			->assertRedirect();

		$this->assertSessionHasErrors(__('book.no_links_to_the_book_were_found_in_the_text'));
	}
}
