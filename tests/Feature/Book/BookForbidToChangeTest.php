<?php

namespace Tests\Feature\Book;

use App\Attachment;
use App\Book;
use App\BookFile;
use App\BookKeyword;
use App\Section;
use App\User;
use Tests\TestCase;

class BookForbidToChangeTest extends TestCase
{
	public function setUp(): void
	{
		parent::setUp();
	}

	public function testAttribute()
	{
		$book = factory(Book::class)->create();
		$book->forbid_to_change = true;
		$book->save();

		$this->assertTrue($book->forbid_to_change);

		$book->forbid_to_change = false;
		$book->save();

		$this->assertFalse($book->forbid_to_change);
	}

	public function testIsCanChange()
	{
		$book = factory(Book::class)->create();
		$book->forbid_to_change = false;
		$book->save();

		$user = factory(User::class)->create();
		$user->group->enable_disable_changes_in_book = false;
		$user->save();

		$this->assertTrue($book->isCanChange($user));

		$book->forbid_to_change = true;
		$book->save();
		$user->group->enable_disable_changes_in_book = false;
		$user->save();

		$this->assertFalse($book->isCanChange($user));

		$book->forbid_to_change = true;
		$book->save();
		$user->group->enable_disable_changes_in_book = true;
		$user->save();

		$this->assertTrue($book->isCanChange($user));

		$book->forbid_to_change = false;
		$book->save();
		$user->group->enable_disable_changes_in_book = true;
		$user->save();

		$this->assertTrue($book->isCanChange($user));
	}

	public function testPolicy()
	{
		$user = factory(User::class)
			->states('admin')
			->create();
		$user->group->enable_disable_changes_in_book = false;
		$user->save();

		$book = factory(Book::class)
			->states('with_cover')
			->create(['create_user_id' => $user->id]);
		$book->forbid_to_change = true;
		$book->save();

		$file = factory(BookFile::class)
			->states('txt')
			->create(['book_id' => $book->id]);

		$section = factory(Section::class)
			->create(['book_id' => $book->id]);

		$attachment = factory(Attachment::class)
			->create(['book_id' => $book->id]);

		$book->refresh();

		$this->assertFalse($user->can('update', $book));
		//$this->assertTrue($user->can('addForReview', $book));
		//$this->assertTrue($user->can('makeAccepted', $book));
		$this->assertTrue($user->can('addToPrivate', $book));
		$this->assertFalse($user->can('delete', $book));
		$this->assertFalse($user->can('restore', $book));
		$this->assertFalse($user->can('group', $book));
		$this->assertFalse($user->can('ungroup', $book));
		$this->assertFalse($user->can('make_main_in_group', $book));
		$this->assertFalse($user->can('change_access', $book));
		$this->assertTrue($user->can('commentOn', $book));
		$this->assertFalse($user->can('add_similar_book', $book));
		$this->assertTrue($user->can('view_section_list', $book));
		$this->assertFalse($user->can('retry_failed_parse', $book));
		$this->assertFalse($user->can('refresh_counters', $book));
		$this->assertFalse($user->can('open_comments', $book));
		$this->assertFalse($user->can('close_comments', $book));
		$this->assertTrue($user->can('read', $book));
		$this->assertTrue($user->can('view_download_files', $book));
		$this->assertTrue($user->can('download', $book));
		$this->assertTrue($user->can('read_or_download', $book));
		$this->assertFalse($user->can('attachAward', $book));
		$this->assertFalse($user->can('set_as_new_read_online_format', $book));
		$this->assertFalse($user->can('cancel_parse', $book));

		$this->assertFalse($user->can('create_section', $book));
		$this->assertFalse($user->can('update', $section));
		$this->assertFalse($user->can('delete', $section));
		$this->assertFalse($user->can('save_sections_position', $book));
		$this->assertFalse($user->can('move_sections_to_notes', $book));

		$this->assertFalse($user->can('create_attachment', $book));
		$this->assertFalse($user->can('delete', $attachment));
		$this->assertFalse($user->can('setAsCover', $attachment));
		$this->assertFalse($user->can('remove_cover', $book));

		$book_keyword = factory(BookKeyword::class)
			->create(['book_id' => $book->id]);

		$this->assertTrue($user->can('addKeywords', $book));
		$this->assertTrue($user->can('delete', $book_keyword));
		$this->assertTrue($user->can('vote', $book_keyword));
		$this->assertTrue($user->can('viewOnCheck', $book_keyword));

		$this->assertFalse($user->can('addFiles', $book));
		$this->assertFalse($user->can('update', $file));
		$this->assertFalse($user->can('delete', $file));
		$this->assertFalse($user->can('restore', $file));
		$this->assertFalse($user->can('set_source_and_make_pages', $file));
	}

	public function testEnableForbidChangesInBookPolicy()
	{
		$user = factory(User::class)->create();
		$user->group->enable_disable_changes_in_book = false;
		$user->save();

		$book = factory(Book::class)->create();
		$book->forbid_to_change = false;
		$book->save();

		$this->assertFalse($user->can('enableForbidChangesInBook', $book));
		$this->assertFalse($user->can('disableForbidChangesInBook', $book));

		$user->group->enable_disable_changes_in_book = true;
		$user->save();

		$this->assertTrue($user->can('enableForbidChangesInBook', $book));
		$this->assertFalse($user->can('disableForbidChangesInBook', $book));

		$book->forbid_to_change = true;
		$book->save();

		$this->assertFalse($user->can('enableForbidChangesInBook', $book));
		$this->assertTrue($user->can('disableForbidChangesInBook', $book));
	}

	public function testEnableForbidChangesInBookHttp()
	{
		$user = factory(User::class)
			->states('admin')
			->create();

		$book = factory(Book::class)->create();

		$this->actingAs($user)
			->get(route('books.forbid_changes.enable', $book))
			->assertRedirect();

		$book->refresh();

		$this->assertTrue($book->forbid_to_change);
	}

	public function testDisableForbidChangesInBookHttp()
	{
		$user = factory(User::class)
			->states('admin')
			->create();

		$book = factory(Book::class)->create();
		$book->forbid_to_change = true;
		$book->save();

		$this->actingAs($user)
			->get(route('books.forbid_changes.disable', $book))
			->assertRedirect();

		$book->refresh();

		$this->assertFalse($book->forbid_to_change);
	}
}