<?php

namespace Tests\Feature\Book;

use App\Book;
use App\User;
use Tests\TestCase;

class BookDeletingOnlineReadAndFilesTest extends TestCase
{
    public function testDeleteBookFile()
    {
        $book = Book::factory()->with_accepted_file()->create();

        $file = $book->files()->first();

        $this->assertEquals(1, $book->files_count);

        $book->deletingOnlineReadAndFiles();

        $file->refresh();
        $book->refresh();

        $this->assertTrue($file->trashed());
        $this->assertEquals(0, $book->files_count);
    }

    public function testDeleteSections()
    {
        $book = Book::factory()->with_section()->create();

        $book->refresh();

        $section = $book->sections()->first();

        $this->assertEquals(1, $book->sections_count);
        $this->assertNotEmpty($book->page_count);

        $book->deletingOnlineReadAndFiles();

        $section->refresh();
        $book->refresh();

        $this->assertTrue($section->trashed());
        $this->assertEquals(0, $book->sections_count);
        $this->assertEquals(0, $book->page_count);
    }

    public function testDeleteNotes()
    {
        $book = Book::factory()->with_note()->create();

        $note = $book->sections()->notes()->first();

        $this->assertEquals(1, $book->notes_count);

        $book->deletingOnlineReadAndFiles();

        $note->refresh();
        $book->refresh();

        $this->assertTrue($note->trashed());
        $this->assertEquals(0, $book->notes_count);
    }

    public function testDeleteAttachmentsButNotCover()
    {
        $book = Book::factory()->with_cover()->with_attachment()->create();

        $cover = $book->cover;
        $attachment = $book->attachments()->where('id', '!=', $cover->id)->first();

        $this->assertFalse($cover->is($attachment));

        $this->assertEquals(2, $book->attachments_count);

        $book->deletingOnlineReadAndFiles();

        $attachment->refresh();
        $book->refresh();
        $cover->refresh();

        $this->assertFalse($cover->trashed());
        $this->assertTrue($attachment->trashed());
        $this->assertEquals(1, $book->attachments_count);
    }

    public function testDisableAccess()
    {
        $book = Book::factory()->with_read_and_download_access()->create();

        $this->assertTrue($book->isReadAccess());
        $this->assertTrue($book->isDownloadAccess());

        $book->deletingOnlineReadAndFiles();
        $book->refresh();

        $this->assertFalse($book->isReadAccess());
        $this->assertFalse($book->isDownloadAccess());
    }

    public function testRouteDeletingOnlineReadAndFilesIsOk()
    {
        config(['activitylog.enabled' => true]);

        $user = User::factory()->admin()->create();

        $book = Book::factory()->with_section()->create();

        $section = $book->sections()->first();

        $this->actingAs($user)
            ->get(route('books.deleting_online_read_and_files', $book))
            ->assertRedirect(route('books.show', $book))
            ->assertSessionHas(['success' => __('book.removed_all_files_chapters_footnotes_and_images_of_the_book')]);

        $section->refresh();

        $this->assertTrue($section->trashed());

        $activity = $book->activities()->first();

        $this->assertEquals(1, $book->activities()->count());
        $this->assertEquals('deleting_online_read_and_files', $activity->description);
        $this->assertEquals($user->id, $activity->causer_id);
        $this->assertEquals('user', $activity->causer_type);
    }

    public function testDeletingOnlineReadAndFilesPolicy()
    {
        $user = User::factory()->create();

        $book = Book::factory()->create();

        $this->assertFalse($user->can('deletingOnlineReadAndFiles', $book));

        $user->group->deleting_online_read_and_files = true;
        $user->push();
        $user->refresh();

        $this->assertTrue($user->can('deletingOnlineReadAndFiles', $book));
    }

    public function testToNewOnlineReadFormat()
    {
        $book = Book::factory()->create();
        $book->online_read_new_format = false;
        $book->save();

        $book->deletingOnlineReadAndFiles();

        $book->refresh();

        $this->assertTrue($book->online_read_new_format);
    }

    public function testDontDeleteAnnotation()
    {
        $book = Book::factory()->with_annotation()->create();

        $annotation = $book->annotation;

        $book->deletingOnlineReadAndFiles();

        $annotation->refresh();

        $this->assertFalse($annotation->trashed());
    }
}
