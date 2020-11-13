<?php

namespace Tests\Feature\Book;

use App\Book;
use App\BookReadRememberPage;
use App\BookStatus;
use App\Enums\ReadStatus;
use App\User;
use Tests\TestCase;

class BookStatusTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testSetStatusHttp()
    {
        $user = User::factory()->create();

        $book = Book::factory()->create();
        $book->statusAccepted();
        $book->save();

        $response = $this->actingAs($user)
            ->get(route('books.read_status.store', [
                'book' => $book->id,
                'code' => 'readed'
            ]));
        //dump(session());
        $response->assertRedirect(route('books.show', $book));

        $book_read_status = $user->book_read_statuses()->first();

        $this->assertNotNull($book_read_status);
        $this->assertNotNull($book_read_status->user_updated_at);
        $this->assertEquals($user->id, $book_read_status->user_id);
        $this->assertEquals($book->id, $book_read_status->book_id);
        $this->assertEquals($book->id, $book_read_status->origin_book_id);
        $this->assertEquals('readed', $book_read_status->status);
        $this->assertEquals($book_read_status->book_id, $book_read_status->origin_book_id);
    }

    public function testUpdateExistingStatusHttp()
    {
        $old_time = now()->subMinutes(10);

        $book_read_status = BookStatus::factory()->create();
        $book_read_status->status = 'read_now';
        $book_read_status->user_updated_at = $old_time;
        $book_read_status->save();

        $user = $book_read_status->user;
        $book = $book_read_status->book;

        $response = $this->actingAs($user)
            ->get(route('books.read_status.store', [
                'book' => $book->id,
                'code' => 'readed'
            ]));
        //dump(session());
        $response->assertRedirect(route('books.show', $book));

        $book_read_status = $user->book_read_statuses()->first();

        $this->assertNotNull($book_read_status);
        $this->assertEquals('readed', $book_read_status->status);
        $this->assertGreaterThan($old_time, $book_read_status->user_updated_at);
    }

    public function testDeleteRememberPageIfStatusReaded()
    {
        $user = User::factory()->create();

        $book = Book::factory()->create();
        $book->statusAccepted();
        $book->save();

        $read_remember = BookReadRememberPage::factory()->create(['book_id' => $book->id, 'user_id' => $user->id]);

        $response = $this->actingAs($user)
            ->get(route('books.read_status.store', [
                'book' => $book->id,
                'code' => ReadStatus::ReadLater
            ]));

        $book_read_status = $user->book_read_statuses()->first();

        $this->assertEquals(ReadStatus::ReadLater, $book_read_status->status);

        $remembered_page = $user->remembered_pages()->first();

        $this->assertNotNull($remembered_page);

        $response = $this->actingAs($user)
            ->get(route('books.read_status.store', [
                'book' => $book->id,
                'code' => ReadStatus::Readed
            ]));

        $book_read_status = $user->book_read_statuses()->first();

        $this->assertEquals(ReadStatus::Readed, $book_read_status->status);

        $remembered_page = $user->remembered_pages()->first();

        $this->assertNull($remembered_page);
    }

    public function testIsDisabledFilterWorks()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('users.books.readed', ['user' => $user, 'read_status' => 'readed']))
            ->assertOk();
    }
}
