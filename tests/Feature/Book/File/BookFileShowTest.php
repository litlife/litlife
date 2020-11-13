<?php

namespace Tests\Feature\Book\File;

use App\Book;
use App\BookFile;
use App\Jobs\Book\UpdateBookFilesCount;
use App\User;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BookFileShowTest extends TestCase
{
    public function testIfOnReview()
    {
        config(['litlife.disk_for_files' => 'public']);

        $book = Book::factory()->create();
        $book->statusSentForReview();
        $book->save();
        $book->refresh();

        $book_file = BookFile::factory()->txt()->create(['book_id' => $book->id]);
        $book_file->statusSentForReview();
        $book_file->save();
        UpdateBookFilesCount::dispatch($book);
        $book->refresh();

        $this->get(route('books.files.show', ['book' => $book, 'fileName' => $book_file->name]))
            ->assertRedirect($book_file->url);

        $admin = User::factory()->create();
        $admin->group->book_file_add_check = true;
        $admin->push();

        $user = User::factory()->create();

        $this->actingAs($admin)
            ->get(route('books.files.show', ['book' => $book, 'fileName' => $book_file->name]))
            ->assertRedirect($book_file->url);

        $this->actingAs($user)
            ->get(route('books.files.show', ['book' => $book, 'fileName' => $book_file->name]))
            ->assertRedirect($book_file->url);
    }

    public function testIfPrivate()
    {
        config(['litlife.disk_for_files' => 'public']);

        $book = Book::factory()->with_create_user()->create();
        $book->statusPrivate();
        $book->save();
        $book->refresh();

        $book_file = BookFile::factory()
            ->txt()
            ->create([
                'book_id' => $book->id,
                'create_user_id' => $book->create_user_id
            ]);

        $book_file->statusPrivate();
        $book_file->save();
        UpdateBookFilesCount::dispatch($book);
        $book->refresh();

        $this->get(route('books.files.show', ['book' => $book, 'fileName' => $book_file->name]))
            ->assertStatus(404);

        $admin = User::factory()->create();
        $admin->group->book_file_add_check = true;
        $admin->push();

        $user = User::factory()->create();

        $this->actingAs($admin)
            ->get(route('books.files.show', ['book' => $book, 'fileName' => $book_file->name]))
            ->assertStatus(404);

        $this->actingAs($user)
            ->get(route('books.files.show', ['book' => $book, 'fileName' => $book_file->name]))
            ->assertStatus(404);

        $this->actingAs($book->create_user)
            ->get(route('books.files.show', ['book' => $book, 'fileName' => $book_file->name]))
            ->assertRedirect($book_file->url);
    }

    public function testXAccelRedirect()
    {
        $file = BookFile::factory()->txt()->create();

        $user = User::factory()->create();

        $url = Storage::disk($file['storage'])
            ->url($file->dirname.'/'.rawurlencode($file->name));

        $response = $this->actingAs($user)
            ->get(route('books.files.show', ['book' => $file->book, 'fileName' => $file->name]))
            ->assertOk()
            ->assertHeader('Content-Type', 'application/x-force-download')
            ->assertHeader('Content-Disposition', 'attachment; filename="'.$file->name.'"')
            ->assertHeader('X-Accel-Redirect', $url);
    }

    public function testDownloadNameUrlEncode()
    {
        config(['litlife.disk_for_files' => 'public']);

        $book = Book::factory()->accepted()->with_create_user()->create(['title' => 'Сделаешь']);

        $book_file = BookFile::factory()
            ->txt()
            ->create([
                'book_id' => $book->id,
                'create_user_id' => $book->create_user_id
            ]);

        $book_file->save();
        UpdateBookFilesCount::dispatch($book);
        $book->refresh();

        $this->actingAs($book->create_user)
            ->get(route('books.files.show', ['book' => $book, 'fileName' => $book_file->name]))
            ->assertRedirect($book_file->url);
    }
}
