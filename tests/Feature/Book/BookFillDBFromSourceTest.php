<?php

namespace Tests\Feature\Book;

use App\Book;
use App\BookFile;
use App\Console\Commands\BookFillDBFromSource;
use App\Jobs\Book\BookUpdatePageNumbersJob;
use App\Jobs\Notification\BookFinishParseJob;
use App\Section;
use App\User;
use Exception;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BookFillDBFromSourceTest extends TestCase
{
    public function testCreateNewSectionsOverExisted()
    {
        Storage::fake(config('filesystems.default'));

        $book = Book::factory()->create();

        $command = new BookFillDBFromSource();
        $command->setExtension('epub');
        $command->setBook($book);
        $command->setStream(fopen(__DIR__.'/Books/test.epub', 'r'));
        $command->addFromFile();

        $book->refresh();
        $section = $book->sections()->first();

        $this->assertEquals(3, $book->sections()->count());

        $response = $this->get(route('books.sections.show', ['book' => $book, 'section' => $section->inner_id]))
            ->assertOk();

        $command = new BookFillDBFromSource();
        $command->setExtension('epub');
        $command->setBook($book);
        $command->setStream(fopen(__DIR__.'/Books/test.epub', 'r'));
        $command->addFromFile();

        $book->refresh();
        $section = $book->sections()->first();

        $this->assertEquals(3, $book->sections()->count());

        $response = $this->get(route('books.sections.show', ['book' => $book, 'section' => $section->inner_id]))
            ->assertOk();
    }

    public function testAddFromSourceDocx()
    {
        Bus::fake([BookUpdatePageNumbersJob::class, BookFinishParseJob::class]);

        Storage::fake(config('filesystems.default'));

        $book = Book::factory()->create();

        $file = new BookFile;
        $file->zip = true;
        $file->open(__DIR__.'/Books/test.docx');
        $file->source = true;
        $file->statusAccepted();
        $book->files()->save($file);

        $file->refresh();

        $this->assertTrue($file->exists());

        Artisan::call('book:fill_db_from_source', ['book_id' => $book->id]);

        $this->assertGreaterThanOrEqual(1, $book->sections()->count());

        Bus::assertDispatched(BookUpdatePageNumbersJob::class);
        Bus::assertDispatched(BookFinishParseJob::class);
    }

    public function testAddFromSourceRtf()
    {
        Storage::fake(config('filesystems.default'));

        $book = Book::factory()->create();

        $file = new BookFile;
        $file->zip = true;
        $file->open(__DIR__.'/Books/test.rtf');
        $file->statusAccepted();
        $file->source = true;
        $book->files()->save($file);

        Artisan::call('book:fill_db_from_source', ['book_id' => $book->id]);

        $this->assertGreaterThanOrEqual(1, $book->sections()->count());
        $this->assertGreaterThanOrEqual(1, $book->attachments()->count());
    }

    public function testAddFromSourceDoc95()
    {
        Storage::fake(config('filesystems.default'));

        $book = Book::factory()->create();

        $file = new BookFile;
        $file->zip = true;
        $file->open(__DIR__.'/Books/test_95.doc');
        $file->statusAccepted();
        $file->source = true;
        $book->files()->save($file);

        Artisan::call('book:fill_db_from_source', ['book_id' => $book->id]);

        $this->assertGreaterThanOrEqual(1, $book->sections()->count());
    }

    public function testAddFromSourceDoc_97_2000_xp()
    {
        Storage::fake(config('filesystems.default'));

        $book = Book::factory()->create();

        $file = new BookFile;
        $file->zip = true;
        $file->open(__DIR__.'/Books/test_97_2000_xp.doc');
        $file->statusAccepted();
        $file->source = true;
        $book->files()->save($file);

        Artisan::call('book:fill_db_from_source', ['book_id' => $book->id]);

        $this->assertGreaterThanOrEqual(1, $book->sections()->count());
    }

    public function testAddFromZippedSourceDoc95()
    {
        Storage::fake(config('filesystems.default'));

        $book = Book::factory()->create();

        $file = new BookFile;
        $file->zip = true;
        $file->open(__DIR__.'/Books/test_95.doc.zip');
        $file->statusAccepted();
        $file->source = true;
        $book->files()->save($file);
        $file->refresh();

        $this->assertTrue($file->exists());
        $this->assertNotNull($file->getFirstFileInArchive());

        Artisan::call('book:fill_db_from_source', ['book_id' => $book->id]);

        $this->assertGreaterThanOrEqual(1, $book->sections()->count());
    }

    public function testAddFromZippedFb2()
    {
        Storage::fake(config('filesystems.default'));

        $book = Book::factory()->create();
        $book->online_read_new_format = false;
        $book->save();
        $book->refresh();


        $file = new BookFile;
        $file->zip = true;
        $file->open(__DIR__.'/Books/test.fb2.zip');
        $file->statusAccepted();
        $file->source = true;
        $book->files()->save($file);

        Artisan::call('book:fill_db_from_source', ['book_id' => $book->id]);

        $book->refresh();

        $this->assertGreaterThanOrEqual(1, $book->sections()->count());
        $this->assertGreaterThanOrEqual(1, $book->attachments()->count());
        $this->assertTrue($book->isPagesNewFormat());
    }

    public function testBookConvertFailed()
    {
        Storage::fake(config('filesystems.default'));

        $book = Book::factory()->create();
        $book->online_read_new_format = false;
        $book->save();
        $book->refresh();

        $file = new BookFile;
        $file->zip = true;
        $file->open(__DIR__.'/Books/test.fb2.zip');
        $file->statusAccepted();
        $file->source = true;
        $book->files()->save($file);

        $file->forceDelete();

        $this->assertNull($book->parse->started_at);
        $this->assertNull($book->parse->waited_at);

        try {
            Artisan::call('book:fill_db_from_source', ['book_id' => $book->id]);
        } catch (Exception $exception) {
            $this->assertStringContainsString('Source file not found', $exception->getMessage());
        }

        $book->refresh();

        $this->assertNotNull($book->parse->created_at);
        $this->assertNotNull($book->parse->started_at);
        $this->assertNotNull($book->parse->failed_at);

        $this->assertContains('Source file not found', $book->parse->getErrors());
        $this->assertFalse($book->isPagesNewFormat());
    }

    public function testAddFromSourceDoc95AndNotContainSectionTextInAnnotation()
    {
        Storage::fake(config('filesystems.default'));

        $book = Book::factory()->create();
        $book->online_read_new_format = false;
        $book->save();
        $book->refresh();

        $file = new BookFile;
        $file->zip = true;
        $file->open(__DIR__.'/Books/test.docx');
        $file->statusAccepted();
        $file->source = true;
        $book->files()->save($file);

        Artisan::call('book:fill_db_from_source', ['book_id' => $book->id]);

        $book->refresh();

        $section = $book->sections()->first();

        $this->assertEquals('text', $section->title);
        $this->assertStringContainsString('', $section->getContentHandeled());
        $this->assertNull($book->annotation);
        $this->assertTrue($book->isPagesNewFormat());
    }

    public function testFillDbIfBookDeleted()
    {
        Storage::fake(config('filesystems.default'));

        $user = User::factory()->create();

        $book = Book::factory()->create();

        $book->parse->create_user()->associate($user);
        $book->push();

        $file = new BookFile;
        $file->zip = true;
        $file->open(__DIR__.'/Books/test.fb2.zip');
        $file->statusAccepted();
        $file->source = true;
        $book->files()->save($file);

        $file->refresh();
        $this->assertTrue($file->exists());
        $book->delete();

        $this->assertSoftDeleted($book);

        Artisan::call('book:fill_db_from_source', ['book_id' => $book->id]);
    }

    public function testOnlyPagesFb2()
    {
        $book = Book::factory()->with_cover()->with_section()->with_note()->with_annotation()->with_attachment()->create();

        $cover = $book->cover;
        $section = $book->sections()->where('type', 'section')->orderBy('id', 'asc')->first();
        $note = $book->sections()->where('type', 'note')->orderBy('id', 'asc')->first();
        $attachment = $book->sections()->where('id', '!=', $cover->id)->orderBy('id', 'asc')->first();
        $annotation = $book->annotation;

        $this->assertNotNull($cover);
        $this->assertNotNull($section);
        $this->assertNotNull($note);
        $this->assertNotNull($annotation);
        $this->assertNotNull($attachment);

        $book->parse->options = ['only_pages'];

        $command = new BookFillDBFromSource();
        $command->setExtension('fb2');
        $command->setBook($book);
        $command->setStream(fopen(__DIR__.'/Books/test.fb2', 'r'));
        $command->addFromFile();

        $book->refresh();

        $this->assertEquals($annotation->id, $book->annotation->id);
        $this->assertEquals($cover->id, $book->cover->id);
        $this->assertNotEquals($section->id, $book->sections()->where('type', 'section')->orderBy('id', 'asc')->first()->id);
        $this->assertNotEquals($note->id, $book->sections()->where('type', 'note')->orderBy('id', 'asc')->first()->id);
        $this->assertNotEquals($attachment->id, $book->sections()->where('id', '!=', $cover->id)->orderBy('id', 'asc')->first()->id);
    }

    public function testOnlyPagesEpub()
    {
        $book = Book::factory()->with_cover()->with_section()->with_note()->with_annotation()->with_attachment()->create();

        $cover = $book->cover;
        $section = $book->sections()->where('type', 'section')->orderBy('id', 'asc')->first();
        $note = $book->sections()->where('type', 'note')->orderBy('id', 'asc')->first();
        $attachment = $book->sections()->where('id', '!=', $cover->id)->orderBy('id', 'asc')->first();
        $annotation = $book->annotation;

        $this->assertNotNull($cover);
        $this->assertNotNull($section);
        $this->assertNotNull($note);
        $this->assertNotNull($annotation);
        $this->assertNotNull($attachment);

        $book->parse->options = ['only_pages'];

        $command = new BookFillDBFromSource();
        $command->setExtension('epub');
        $command->setBook($book);
        $command->setStream(fopen(__DIR__.'/Books/test.epub', 'r'));
        $command->addFromFile();

        $book->refresh();

        $this->assertEquals($annotation->id, $book->annotation->id);
        $this->assertEquals($cover->id, $book->cover->id);
        $this->assertNotEquals($section->id, $book->sections()->where('type', 'section')->orderBy('id', 'asc')->first()->id);
        $this->assertNotEquals($attachment->id, $book->sections()->where('id', '!=', $cover->id)->orderBy('id', 'asc')->first()->id);
    }

    public function testIsInProgressNotConfirm()
    {
        $book = Book::factory()->create();
        $book->parse->start();
        $book->push();

        $this->artisan('book:fill_db_from_source', ['book_id' => $book->id])
            ->expectsQuestion('The book is already being processed. Want to continue anyway?', '')
            ->assertExitCode(0);
    }

    public function testIsInProgressConfirm()
    {
        $book = Book::factory()->create();
        $book->parse->start();
        $book->push();

        $this->expectExceptionMessage('Source file not found');

        $this->artisan('book:fill_db_from_source', ['book_id' => $book->id])
            ->expectsQuestion('The book is already being processed. Want to continue anyway?', 'y');

        $book->refresh();

        $this->assertTrue($book->parse->isFailed());
    }

    public function testEpub()
    {
        $book = Book::factory()->create([
            'create_user_id' => 50000,
            'is_si' => false,
            'is_lp' => false,
            'age' => 0
        ]);

        $command = new BookFillDBFromSource();
        $command->setExtension('epub');
        $command->setBook($book);
        $command->setStream(fopen(__DIR__.'/Books/test.epub', 'r'));
        $command->addFromFile();

        $book->refresh();

        $this->assertEquals(1, $book->attachments->count());

        $sections = Section::scoped(['book_id' => $book->id, 'type' => 'section'])
            ->defaultOrder()
            ->get();

        $this->assertStringContainsString('<a href="#u-section-2">note</a>', $sections[0]->getContent());
        $this->assertStringContainsString('<a href="#u-section-2">sit</a>', $sections[0]->getContent());
        $this->assertStringContainsString('<a href="#u-anchor1">to anchor</a>', $sections[1]->getContent());
        $this->assertEquals('u-section-1', $sections[0]->parameters['section_id']);
        $this->assertEquals('u-section-2', $sections[1]->parameters['section_id']);

        $this->assertStringContainsString($book->attachments->first()->url, $sections[0]->getContent());

        $this->assertEquals('[Title here]', $book->title);

        $genres = $book->genres()->orderBy('fb_code', 'asc')->get();

        $this->assertEquals('sci_anachem', $genres[0]->fb_code);
        $this->assertEquals('music', $genres[1]->fb_code);

        $authors = $book->writers()->any()->orderBy('id', 'asc')->get();

        $this->assertEquals('Author First Name', $authors[0]->fullName);
        $this->assertEquals('Author2 First2 Name2', $authors[1]->fullName);

        $sequences = $book->sequences()->any()->orderBy('id', 'asc')->get();

        $this->assertEquals('SequenceName', $sequences[0]->name);
        $this->assertEquals('1', $sequences[0]->pivot->number);
        $this->assertEquals('0', $sequences[0]->pivot->order);

        $this->assertEquals('SequenceName2', $sequences[1]->name);
        $this->assertNull($sequences[1]->pivot->number);
        $this->assertEquals('1', $sequences[1]->pivot->order);

        $translators = $book->translators()->any()->orderBy('id', 'asc')->get();

        $this->assertEquals('Translator First Name', $translators->first()->fullName);

        $this->assertEquals('111-1-111-11111-1', $book->pi_isbn);
        $this->assertEquals('rightsholder', $book->rightholder);
        $this->assertEquals('<p>Annotation</p>', $book->annotation->getContent());
        $this->assertEquals('2002', $book->pi_year);
        $this->assertEquals('Publisher', $book->pi_pub);
        $this->assertEquals('City', $book->pi_city);
        $this->assertEquals('2001', $book->year_writing);

        //$this->assertEquals('Publisher', $book->pi_pub);
        $this->assertNotNull($book->cover);
        $this->assertEquals('test.png', $book->cover->name);

        $this->assertNotNull($book->annotation);
        $this->assertEquals('<p>Annotation</p>', $book->annotation->getContent());
    }
}
