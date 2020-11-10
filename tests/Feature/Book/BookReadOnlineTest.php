<?php

namespace Tests\Feature\Book;

use App\Book;
use App\BookReadRememberPage;
use App\Section;
use App\User;
use Tests\TestCase;

class BookReadOnlineTest extends TestCase
{
	public function testReadOnlineRedirectNew()
	{
		$book = Book::factory()->create();

		$section = Section::factory()->create(['book_id' => $book->id]);

		$firstSection = $book->sections()
			->chapter()
			->defaultOrder()
			->first();

		$readOnlineUrl = route('books.sections.show', ['book' => $book, 'section' => $firstSection->inner_id]);

		$this->get(route('books.read.online', $book))
			->assertRedirect($readOnlineUrl);
	}

	public function testReadOnlineRedirectOld()
	{
		$book = Book::factory()->create();
		$book->online_read_new_format = false;
		$book->save();

		$readOnlineUrl = route('books.old.page', ['book' => $book]);

		$this->get(route('books.read.online', $book))
			->assertRedirect($readOnlineUrl);
	}

	public function testFirstSectionIfItsPrivate()
	{
		$book = Book::factory()->create();

		$section = Section::factory()->private()->create();

		$section2 = Section::factory()->accepted()->create();

		$sections = $book->sections()->defaultOrder()->get();

		$this->assertEquals($section->id, $sections[0]->id);
		$this->assertEquals($section2->id, $sections[1]->id);

		$this->get(route('books.show', $book))
			->assertOk()
			->assertViewHas(['first_section' => $section2]);

		$this->get(route('books.read.online', $book))
			->assertRedirect(route('books.sections.show', ['book' => $book, 'section' => $section2->inner_id]));
	}

	public function testReadOnlineWithSectionAndPage()
	{
		$book = Book::factory()->with_three_sections()->create();

		$user = User::factory()->create();

		$section = $book->sections()->first();

		$book_read_remember_page = BookReadRememberPage::factory()->create([
				'book_id' => $book->id,
				'user_id' => $user->id,
				'inner_section_id' => $section->inner_id,
				'page' => 4,
				'characters_count' => 0
			]);

		$this->actingAs($user)
			->get(route('books.read.online', ['book' => $book]))
			->assertRedirect(route('books.sections.show', ['book' => $book, 'section' => $section->inner_id, 'page' => 4]));
	}

	public function testReadOnlineRedirectOldWithRememberedPage()
	{
		$book = Book::factory()->create();
		$book->online_read_new_format = false;
		$book->save();

		$user = User::factory()->create();

		$book_read_remember_page = BookReadRememberPage::factory()->create([
				'book_id' => $book->id,
				'user_id' => $user->id,
				'inner_section_id' => null,
				'page' => 4,
				'characters_count' => 0
			]);

		$readOnlineUrl = route('books.old.page', ['book' => $book, 'page' => $book_read_remember_page->page]);

		$this->actingAs($user)
			->get(route('books.read.online', $book))
			->assertRedirect($readOnlineUrl);
	}

	public function testRememberedSectionDeletedIsOk()
	{
		$book = Book::factory()->with_three_sections()->create();

		$user = User::factory()->create();

		$section = $book->sections()->first();

		$book_read_remember_page = BookReadRememberPage::factory()->create([
				'book_id' => $book->id,
				'user_id' => $user->id,
				'inner_section_id' => $section->inner_id,
				'page' => 4,
				'characters_count' => 0
			]);

		$section->delete();

		$url = route('books.sections.show', ['book' => $book, 'section' => $section->inner_id, 'page' => 4]);

		$this->actingAs($user)
			->get(route('books.read.online', $book))
			->assertRedirect($url);

		$this->actingAs($user)
			->get($url)
			->assertNotFound();
	}

	public function testRememberedSectionIsNullOk()
	{
		$book = Book::factory()->with_three_sections()->create();

		$user = User::factory()->create();

		$section = $book->sections()->first();

		$book_read_remember_page = BookReadRememberPage::factory()->create([
				'book_id' => $book->id,
				'user_id' => $user->id,
				'inner_section_id' => null,
				'page' => 4,
				'characters_count' => 0
			]);

		$firstSection = $book->sections()
			->accepted()
			->chapter()
			->defaultOrder()
			->first();

		$url = route('books.sections.show', ['book' => $book, 'section' => $firstSection->inner_id]);

		$this->actingAs($user)
			->get(route('books.read.online', $book))
			->assertRedirect($url);
	}

	public function testShowReadButtonIfAllChaptersArePaid()
	{
		$book = Book::factory()->with_writer()->on_sale()->with_section()->with_read_and_download_access()->create();

		$user = User::factory()->create();

		$section = $book->sections()->first();

		$this->assertTrue($user->can('view_read_button', $book));
		$this->assertFalse($user->can('read', $book));

		$this->actingAs($user)
			->get(route('books.read.online', $book))
			->assertRedirect(route('books.sections.show', ['book' => $book, 'section' => $section->inner_id]));

		$this->actingAs($user)
			->get(route('books.sections.show', ['book' => $book, 'section' => $section->inner_id]))
			->assertRedirect(route('books.purchase', ['book' => $book]));
	}
}
