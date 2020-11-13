<?php

namespace Tests\Feature\Book\Attachment;

use App\Attachment;
use App\Book;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AttachmentTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        Storage::fake(config('filesystems.default'));
    }

    public function testFixAttachmentExtensionIfWrong()
    {
        $book = Book::factory()->private()->create();

        $attachment = new Attachment();
        $attachment->openImage(__DIR__.'/../../images/test.jpg_0');
        $book->attachments()->save($attachment);
        $attachment->refresh();

        $this->assertEquals('test.jpeg', $attachment->name);
    }
}
