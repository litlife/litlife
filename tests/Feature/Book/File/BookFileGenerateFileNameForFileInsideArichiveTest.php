<?php

namespace Tests\Feature\Book\File;

use App\Book;
use App\BookFile;
use Tests\TestCase;

class BookFileGenerateFileNameForFileInsideArichiveTest extends TestCase
{
    public function testGenerateFileNameForFileInsideArichive()
    {
        $file = BookFile::factory()->txt()->zip()->create();
        $file->format = 'fb2';
        $file->save();

        $book = Book::factory()->without_any_authors()->create(['title' => 'Книга']);

        $file->book()->associate($book);

        $this->assertEquals('Kniga.fb2', $file->generateFileNameForFileInsideArichive());
    }
}
