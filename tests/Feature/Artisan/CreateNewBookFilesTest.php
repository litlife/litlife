<?php

namespace Tests\Feature\Artisan;

use App\Book;
use App\Jobs\Book\UpdateBookSectionsCount;
use App\Library\AddEpubFile;
use App\Scopes\CheckedScope;
use App\Section;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CreateNewBookFilesTest extends TestCase
{
    public function testCreateNewBookFileIfBookAccepted()
    {
        $book = Book::factory()->with_three_sections()->accepted()->create();

        $book->needCreateNewBookFiles();
        $book->save();

        Artisan::call('bookfiles:make', ['bookId' => $book->id]);

        $file = $book->files()->first();

        $this->assertTrue($file->isAccepted());
    }

    public function testCreateNewBookFileIfBookPrivate()
    {
        $book = Book::factory()->with_three_sections()->private()->create();

        $book->needCreateNewBookFiles();
        $book->save();

        Artisan::call('bookfiles:make', ['bookId' => $book->id]);

        $file = $book->files()->first();

        $this->assertTrue($file->isPrivate());
    }

    public function testCreateNewBookFileIfBookSentForReview()
    {
        $book = Book::factory()->with_three_sections()->sent_for_review()->create();

        $book->needCreateNewBookFiles();
        $book->save();

        Artisan::call('bookfiles:make', ['bookId' => $book->id]);

        $file = $book->files()->first();

        $this->assertTrue($file->isAccepted());
    }

    public function testCreatedAfterEdit()
    {
        Storage::fake(config('filesystems.default'));

        $book = Book::factory()->private()->create();

        $this->assertTrue($book->isPagesNewFormat());

        $add = new AddEpubFile();
        $add->setBook($book);
        $add->setFile(__DIR__.'/../Book/Books/test.epub');
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

        $book = Book::factory()->private()->with_create_user()->with_section()->create();

        $section = Section::factory()->create(['book_id' => $book->id]);

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

        $this->assertNotNull(Book::anyNotTrashed()->waitedNeedCreateNewBookFiles()->whereNeedCreateNewBookFilesCooldownIsOver()->where('id',
            $book->id)->first());

        $book->needCreateNewBookFilesDisable();
        $book->save();
        $book->refresh();

        $this->assertFalse($book->isWaitedCreateNewBookFiles());
        $this->assertNull(Book::anyNotTrashed()->waitedNeedCreateNewBookFiles()->whereNeedCreateNewBookFilesCooldownIsOver()->where('id', $book->id)->first());
    }

    public function testDontCreateFileIfBookIsWithoutChapters()
    {
        $book = Book::factory()->create();

        $note = Section::factory()->note()->create(['book_id' => $book->id]);

        $this->assertEquals(1, $book->fresh()->notes_count);
        $this->assertEquals(0, $book->characters_count);

        Artisan::call('bookfiles:make', ['bookId' => $book->id]);

        $this->assertEquals(0, $book->files()->count());
    }
}
