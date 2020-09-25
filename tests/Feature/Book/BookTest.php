<?php

namespace Tests\Feature\Book;

use App\Attachment;
use App\Book;
use App\BookFile;
use App\BookKeyword;
use App\Section;
use App\User;
use Illuminate\Support\Str;
use Tests\TestCase;

class BookTest extends TestCase
{
	public function testFulltextSearch()
	{
		$author = Book::FulltextSearch('Время&—&детство!')->get();

		$this->assertTrue(true);
	}

	public function testRefreshCounters()
	{
		$user = factory(User::class)
			->create();

		$book = factory(Book::class)
			->create();

		$this->actingAs($user)
			->get(route('books.refresh_counters', ['book' => $book]))
			->assertRedirect(route('books.show', ['book' => $book]));
	}

	public function testFulltextSearchScopeSpecialSymbols()
	{
		$title = Str::random(10);

		$book = factory(Book::class)
			->states('with_create_user')
			->create(['title' => 'ё' . $title]);

		$this->assertEquals(1, Book::query()->titleAuthorsFulltextSearch('ё' . $title)->count());
		$this->assertEquals(1, Book::query()->titleAuthorsFulltextSearch('е' . $title)->count());
	}

	public function testPrivateBookPolicy()
	{
		$book = factory(Book::class)
			->states('private', 'with_create_user')
			->create();

		$user = $book->create_user;
		$user->group->book_keyword_vote = true;
		$user->push();

		$section = factory(Section::class)->create(['book_id' => $book->id]);
		$attachment = factory(Attachment::class)->create(['book_id' => $book->id]);
		$file = factory(BookFile::class)->states('txt')->create(['book_id' => $book->id]);

		$book->refresh();

		$this->assertTrue($user->can('update', $book));
		$this->assertTrue($user->can('delete', $book));
		$this->assertFalse($user->can('group', $book));
		$this->assertFalse($user->can('ungroup', $book));
		$this->assertFalse($user->can('make_main_in_group', $book));
		$this->assertTrue($user->can('change_access', $book));
		$this->assertTrue($user->can('commentOn', $book));
		$this->assertTrue($user->can('add_similar_book', $book));
		$this->assertTrue($user->can('view_section_list', $book));
		$this->assertTrue($user->can('view_group_books', $book));
		$this->assertFalse($user->can('watch_activity_logs', $book));
		$this->assertFalse($user->can('display_technical_information', $book));
		$this->assertTrue($user->can('refresh_counters', $book));
		$this->assertFalse($user->can('close_comments', $book));
		$this->assertTrue($user->can('read', $book));
		$this->assertTrue($user->can('view_download_files', $book));
		$this->assertTrue($user->can('download', $book));
		$this->assertTrue($user->can('read_or_download', $book));
		$this->assertTrue($user->can('attachAward', $book));
		$this->assertFalse($user->can('view_deleted', $book));

		$this->assertTrue($user->can('create_section', $book));
		$this->assertTrue($user->can('update', $section));
		$this->assertTrue($user->can('delete', $section));
		$this->assertTrue($user->can('save_sections_position', $book));
		$this->assertTrue($user->can('move_sections_to_notes', $book));

		$this->assertTrue($user->can('create_attachment', $book));
		$this->assertTrue($user->can('delete', $attachment));
		$this->assertTrue($user->can('setAsCover', $attachment));

		$this->assertTrue($user->can('addFiles', $book));
		$this->assertTrue($user->can('update', $file));
		$this->assertTrue($user->can('delete', $file));
		$this->assertTrue($user->can('set_source_and_make_pages', $file));

		$book_keyword = factory(BookKeyword::class)
			->create(['book_id' => $book->id]);

		$this->assertTrue($user->can('addKeywords', $book));
		$this->assertTrue($user->can('delete', $book_keyword));
		$this->assertTrue($user->can('vote', $book_keyword));
	}

	public function testAutoCreateAverageRatingForPeriodInDatabase()
	{
		$user = factory(User::class)->create();

		$book = new Book();
		$book->title = Str::random(8);
		$book->create_user()->associate($user);
		$book->save();

		$this->assertDatabaseHas('books_average_rating_for_period', [
			'book_id' => $book->id,
			'day_vote_average' => 0
		]);
	}
}
