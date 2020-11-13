<?php

namespace Tests\Feature\Book\Attachment;

use App\Attachment;
use App\Book;
use App\Jobs\AttachmentRenameJob;
use App\Section;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AttachmentRenameJobTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        Storage::fake(config('filesystems.default'));
    }

    public function testRename()
    {
        $book = Book::factory()->private()->create();

        $attachment = new Attachment();
        $attachment->openImage(__DIR__.'/../../images/test.jpeg');
        $book->attachments()->save($attachment);
        $attachment->refresh();

        $attachment2 = new Attachment();
        $attachment2->openImage(__DIR__.'/../../images/test.gif');
        $book->attachments()->save($attachment2);
        $attachment2->refresh();

        $section = new Section();
        $section->title = uniqid();
        $section->type = 'section';
        $section->content = '<p><img src="'.$attachment->url.'" /></p>';
        $book->sections()->save($section);
        $section->refresh();

        $section2 = new Section();
        $section2->title = uniqid();
        $section2->type = 'section';
        $section2->content = '<p><img src="'.$attachment2->url.'" /></p>';
        $book->sections()->save($section2);
        $section2->refresh();

        $note = new Section();
        $note->title = uniqid();
        $note->type = 'note';
        $note->content = '<p><img src="'.$attachment->url.'" /></p>';
        $book->sections()->save($note);
        $section->refresh();

        $this->assertTrue($attachment->exists());
        $this->assertFalse($attachment->isZipArchive());

        AttachmentRenameJob::dispatch($book, $attachment, 'new_name.jpeg');

        $attachment->refresh();
        $section->refresh();
        $section2->refresh();
        $note->refresh();

        $this->assertEquals('<p><img src="/storage/'.$attachment->dirname.'/new_name.jpeg" alt="new_name.jpeg"/></p>',
            $section->getContent());

        $this->assertEquals('<p><img src="/storage/'.$attachment->dirname.'/new_name.jpeg" alt="new_name.jpeg"/></p>',
            $note->getContent());

        $this->assertEquals('<p><img src="/storage/'.$attachment2->dirname.'/test.gif" alt="test.gif"/></p>',
            $section2->getContent());
    }
}
