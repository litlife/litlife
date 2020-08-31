<?php

namespace Tests\Feature\Book;

use App\Book;
use App\BookFile;
use App\Jobs\Book\UpdateBookSectionsCount;
use App\Library\AddEpubFile;
use App\Scopes\CheckedScope;
use App\Section;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BookNewFilesCreateTest extends TestCase
{
	/**
	 * A basic test example.
	 *
	 * @return void
	 */
	public function testDisableIfBookOnParse()
	{
		Storage::fake(config('filesystems.default'));

		$user = factory(User::class)->create();
		$user->group->book_fb2_file_convert_divide_on_page = true;
		$user->group->edit_self_book = true;
		$user->group->edit_other_user_book = true;
		$user->push();

		$book = factory(Book::class)
			->create(['create_user_id' => $user->id])
			->fresh();

		$file = new BookFile;
		$file->source = true;
		$file->open(__DIR__ . '/Books/test.epub');
		$file->statusAccepted();
		$book->files()->save($file);

		Artisan::call('book:fill_db_from_source', ['book_id' => $book->id]);

		$this->assertTrue($book->refresh()->isWaitedCreateNewBookFiles());
		$this->assertTrue($book->isPagesNewFormat());

		$this->actingAs($user)
			->patch(route('books.sections.update', ['book' => $book, 'section' => $book->sections()->where('type', 'section')->first()->inner_id]),
				[
					'title' => $this->faker->realText(100),
					'content' => $this->faker->text
				])
			->assertSessionHasNoErrors()
			->assertRedirect();

		$this->assertTrue($book->refresh()->isWaitedCreateNewBookFiles());

		$file = new BookFile;
		$file->open(__DIR__ . '/Books/test.fb2');
		$file->statusAccepted();
		$book->files()->save($file);

		$this->actingAs($user)
			->get(route('book_files.set_source_and_make_pages', ['file' => $file]))
			->assertSessionHasNoErrors()
			->assertRedirect();

		$book->refresh();

		$this->assertTrue($book->parse->isWait());
		$this->assertFalse($book->isWaitedCreateNewBookFiles());
	}

	public function testCreatedAfterEdit()
	{
		Storage::fake(config('filesystems.default'));

		$book = factory(Book::class)
			->states('private')->create();

		$this->assertTrue($book->isPagesNewFormat());

		$add = new AddEpubFile();
		$add->setBook($book);
		$add->setFile(__DIR__ . '/Books/test.epub');
		$add->init();

		UpdateBookSectionsCount::dispatch($book);

		Artisan::call('bookfiles:make', ['bookId' => $book->id]);

		$book->refresh();

		$book->needCreateNewBookFiles();
		$book->save();
		$book->refresh();

		$file = $book->files()->anyNotTrashed()->first();

		$this->assertNotNull($file);
		$this->assertEquals(1, $book->files()->withoutGlobalScope(CheckedScope::class)->count());
		$this->assertTrue($file->isAutoCreated());

		Artisan::call('bookfiles:make', ['bookId' => $book->id]);

		$book->refresh();

		$new_file = $book->files()->anyNotTrashed()->first();

		$this->assertNotNull($file);
		$this->assertNotNull($file);
		$this->assertNotEquals($file->id, $new_file->id);
		$this->assertEquals($book->status, $file->status);
		$this->assertEquals($book->status, $new_file->status);
		$this->assertEquals(1, $book->files()->withoutGlobalScope(CheckedScope::class)->count());
		$this->assertTrue($file->isAutoCreated());
	}

	public function testCooldown()
	{
		Storage::fake(config('filesystems.default'));

		$book = factory(Book::class)->create()->fresh();
		$book->statusPrivate();
		$book->sections_count = 1;
		$book->save();

		$section = factory(Section::class)
			->create(['book_id' => $book->id]);

		$this->actingAs($book->create_user)
			->patch(route('books.sections.update', ['book' => $book, 'section' => $book->sections()->first()->inner_id]),
				[
					'title' => $this->faker->realText(100),
					'content' => $this->faker->realText(100)
				])
			->assertSessionHasNoErrors()
			->assertRedirect();

		$book->refresh();

		$this->assertTrue($book->fresh()->isWaitedCreateNewBookFiles());
		$this->assertNotNull(Book::anyNotTrashed()->waitedNeedCreateNewBookFiles()->where('id', $book->id)->first());

		$this->assertNull(Book::anyNotTrashed()->waitedNeedCreateNewBookFiles()->whereNeedCreateNewBookFilesCooldownIsOver()->where('id', $book->id)->first());

		$this->assertEquals(config('litlife.cooldown_for_create_new_book_files_after_edit') - 1, $book->fresh()->minutesTillNewFilesWillBeCreated());

		Carbon::setTestNow(now()->addMinutes(1)->addSeconds(40));

		$this->assertNull(Book::anyNotTrashed()->waitedNeedCreateNewBookFiles()->whereNeedCreateNewBookFilesCooldownIsOver()->where('id', $book->id)->first());

		$this->assertEquals((config('litlife.cooldown_for_create_new_book_files_after_edit') - 2), $book->fresh()->minutesTillNewFilesWillBeCreated());

		Carbon::setTestNow(now()->addMinutes(config('litlife.cooldown_for_create_new_book_files_after_edit'))->addMinute());

		$this->assertNotNull(Book::anyNotTrashed()->waitedNeedCreateNewBookFiles()->whereNeedCreateNewBookFilesCooldownIsOver()->where('id', $book->id)->first());

		$book->needCreateNewBookFilesDisable();
		$book->save();
		$book->refresh();

		$this->assertFalse($book->isWaitedCreateNewBookFiles());
		$this->assertNull(Book::anyNotTrashed()->waitedNeedCreateNewBookFiles()->whereNeedCreateNewBookFilesCooldownIsOver()->where('id', $book->id)->first());
	}

	public function testDontCreateFileIfBookIsWithoutChapters()
	{
		$book = factory(Book::class)
			->create();

		$note = factory(Section::class)
			->states('note')
			->create(['book_id' => $book->id]);

		$this->assertEquals(1, $book->fresh()->notes_count);
		$this->assertEquals(0, $book->characters_count);

		Artisan::call('bookfiles:make', ['bookId' => $book->id]);

		$this->assertEquals(0, $book->files()->count());
	}
}
