<?php

namespace Tests\Feature\Book\TextProcessing;

use App\Book;
use App\BookTextProcessing;
use App\User;
use Illuminate\Support\Str;
use Tests\TestCase;

class BookTextProcessingTest extends TestCase
{
    public function testIndexRouteIsOk()
    {
        $book = Book::factory()->create();

        $user = User::factory()->create();
        $user->group->create_text_processing_books = true;
        $user->push();

        $this->actingAs($user)
            ->get(route('books.text_processings.index', $book))
            ->assertOk()
            ->assertSeeText(__('book_text_processing.no_text_processing_has_been_created_yet'));
    }

    public function testCreateRouteIsOk()
    {
        $book = Book::factory()->create();

        $user = User::factory()->create();
        $user->group->create_text_processing_books = true;
        $user->push();

        $this->actingAs($user)
            ->get(route('books.text_processings.create', $book))
            ->assertOk()
            ->assertSeeText(__('book_text_processing.remove_bold'))
            ->assertSeeText(__('book_text_processing.remove_extra_spaces'))
            ->assertSeeText(__('book_text_processing.split_into_chapters'));
    }

    public function testStoreRouteIsOk()
    {
        $book = Book::factory()->create();

        $user = User::factory()->create();
        $user->group->create_text_processing_books = true;
        $user->push();

        $this->assertFalse($book->forbid_to_change);

        $this->actingAs($user)
            ->post(route('books.text_processings.index', $book), [
                'remove_bold' => true,
                'remove_extra_spaces' => true,
                'split_into_chapters' => true,
                'merge_paragraphs_if_there_is_no_dot_at_the_end' => true
            ])
            ->assertRedirect(route('books.show', $book))
            ->assertSessionHas(['success' => __('book_text_processing.processing_a_text_is_successfully_created')]);

        $processing = $book->textProcessings()->first();

        $book->refresh();

        $this->assertTrue($book->forbid_to_change);
        $this->assertNotNull($processing);
        $this->assertTrue($processing->remove_bold);
        $this->assertTrue($processing->remove_extra_spaces);
        $this->assertTrue($processing->split_into_chapters);
        $this->assertTrue($processing->merge_paragraphs_if_there_is_no_dot_at_the_end);
        $this->assertEquals($user->id, $processing->create_user_id);
    }

    public function testWait()
    {
        $processing = new BookTextProcessing();

        $processing->started_at = now();
        $processing->completed_at = now();

        $processing->wait();

        $this->assertNull($processing->started_at);
        $this->assertNull($processing->completed_at);
    }

    public function testComplete()
    {
        $processing = new BookTextProcessing();

        $this->assertNull($processing->completed_at);

        $processing->complete();

        $this->assertNotNull($processing->completed_at);
    }

    public function testStart()
    {
        $processing = new BookTextProcessing();

        $this->assertNull($processing->started_at);

        $processing->start();

        $this->assertNotNull($processing->started_at);
    }

    public function testIsWait()
    {
        $processing = new BookTextProcessing();

        $this->assertTrue($processing->isWait());

        $processing->start();

        $this->assertFalse($processing->isWait());
    }

    public function testIsCompleted()
    {
        $processing = new BookTextProcessing();

        $this->assertFalse($processing->isCompleted());

        $processing->complete();

        $this->assertTrue($processing->isCompleted());
    }

    public function testIsStarted()
    {
        $processing = new BookTextProcessing();

        $this->assertFalse($processing->isStarted());

        $processing->start();

        $this->assertTrue($processing->isStarted());

        $processing->complete();

        $this->assertFalse($processing->isStarted());
    }

    public function testWaitedScope()
    {
        $processing = BookTextProcessing::factory()->create();
        $processing->wait();
        $processing->save();

        $this->assertEquals(1, BookTextProcessing::where('id', $processing->id)->waited()->count());

        $processing->start();
        $processing->save();

        $this->assertEquals(0, BookTextProcessing::where('id', $processing->id)->waited()->count());

        $processing->complete();
        $processing->save();

        $this->assertEquals(0, BookTextProcessing::where('id', $processing->id)->waited()->count());
    }

    public function testIsAllValueFalseError()
    {
        $book = Book::factory()->create();

        $user = User::factory()->create();
        $user->group->create_text_processing_books = true;
        $user->push();

        $this->assertFalse($book->forbid_to_change);

        $this->actingAs($user)
            ->get(route('books.text_processings.create', $book))
            ->assertOk();

        $this->actingAs($user)
            ->post(route('books.text_processings.store', $book), [
                '_token' => Str::random(8),
                'remove_bold' => false,
                'remove_extra_spaces' => false,
                'split_into_chapters' => false
            ])
            ->assertRedirect(route('books.text_processings.create', $book))
            ->assertSessionHasErrors(['split_into_chapters' => __('book_text_processing.at_least_one_item_must_be_marked')]);

        $processing = $book->textProcessings()->first();

        $this->assertNull($processing);
    }
}
