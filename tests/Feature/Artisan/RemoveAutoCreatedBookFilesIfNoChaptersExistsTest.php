<?php

namespace Tests\Feature\Artisan;

use App\BookFile;
use App\Section;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class RemoveAutoCreatedBookFilesIfNoChaptersExistsTest extends TestCase
{
    public function testDeleteEpubFilesIfCharacterCountEmpty()
    {
        $file = BookFile::factory()->txt()->create();
        $file->format = 'epub';
        $file->save();

        $book = $file->book;

        $this->assertEquals(1, $book->files()->count());
        $this->assertEquals(0, $book->characters_count);
        $this->assertTrue($book->isPagesNewFormat());

        Artisan::call('clear:remove_auto_created_book_files_if_no_chapters_exists', ['min_id' => $book->id]);

        $book->fresh();

        $this->assertEquals(0, $book->files()->count());
    }

    public function testDontDeleteOtherFiles()
    {
        $file = BookFile::factory()->txt()->create();
        $file->format = 'epub';
        $file->save();

        $book = $file->book;

        $other_file = BookFile::factory()->txt()->create(['book_id' => $book->id]);
        $other_file->format = 'pdf';
        $other_file->save();

        $this->assertEquals(2, $book->files()->count());
        $this->assertEquals(0, $book->characters_count);
        $this->assertTrue($book->isPagesNewFormat());

        Artisan::call('clear:remove_auto_created_book_files_if_no_chapters_exists', ['min_id' => $book->id]);

        $book->fresh();

        $this->assertEquals(1, $book->files()->count());
        $this->assertEquals('pdf', $book->files()->first()->format);
    }

    public function testDontDeleteIfChapterExists()
    {
        $file = BookFile::factory()->txt()->create();
        $file->format = 'epub';
        $file->save();

        $book = $file->book;

        $section = Section::factory()->create(['book_id' => $book->id]);
        $section->content = '';
        $section->save();

        $book->refresh();

        $this->assertEquals(1, $book->files()->count());
        $this->assertEquals(0, $book->characters_count);
        $this->assertEquals(1, $book->sections_count);
        $this->assertTrue($book->isPagesNewFormat());

        Artisan::call('clear:remove_auto_created_book_files_if_no_chapters_exists', ['min_id' => $book->id]);

        $book->fresh();

        $this->assertEquals(1, $book->files()->count());
    }

    public function testDontDeleteIfCharactersNotEmpty()
    {
        $file = BookFile::factory()->txt()->create();
        $file->format = 'epub';
        $file->save();

        $book = $file->book;

        $section = Section::factory()->create(['book_id' => $book->id]);
        $section->content = 'test';
        $section->save();

        $book->refresh();

        $this->assertEquals(1, $book->files()->count());
        $this->assertEquals(4, $book->characters_count);
        $this->assertTrue($book->isPagesNewFormat());

        Artisan::call('clear:remove_auto_created_book_files_if_no_chapters_exists', ['min_id' => $book->id]);

        $book->fresh();

        $this->assertEquals(1, $book->files()->count());
    }
}
