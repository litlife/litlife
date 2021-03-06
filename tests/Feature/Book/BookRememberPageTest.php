<?php

namespace Tests\Feature\Book;

use App\Book;
use App\BookReadRememberPage;
use App\Jobs\Book\BookUpdatePageNumbersJob;
use App\Section;
use App\User;
use App\UserBook;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class BookRememberPageTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testRemeberHttp()
    {
        $book = Book::factory()->with_create_user()->create();

        $section = Section::factory()->create(['book_id' => $book->id]);

        $section2 = Section::factory()->create(['book_id' => $book->id]);

        $book->refresh();

        $response = $this->actingAs($book->create_user)
            ->get(route('books.sections.show', ['book' => $book->id, 'section' => $section->inner_id]))
            ->assertOk();

        $remember_page = $book->create_user->remembered_pages()->first();

        $this->assertEquals(1, $book->create_user->remembered_pages()->count());
        $this->assertEquals($book->create_user->id, $remember_page->user_id);
        $this->assertEquals($book->id, $remember_page->book_id);
        $this->assertEquals($section->inner_id, $remember_page->inner_section_id);
        $this->assertEquals(1, $remember_page->page);
        $this->assertEquals($book->characters_count, $remember_page->characters_count);

        $book->characters_count = rand(100, 100000);
        $book->save();

        $response = $this->actingAs($book->create_user)
            ->get(route('books.sections.show', ['book' => $book->id, 'section' => $section2->inner_id]))
            ->assertOk();

        $remember_page = $book->create_user->remembered_pages()->first();

        $this->assertEquals(1, $book->create_user->remembered_pages()->count());
        $this->assertEquals($book->create_user->id, $remember_page->user_id);
        $this->assertEquals($book->id, $remember_page->book_id);
        $this->assertEquals($section2->inner_id, $remember_page->inner_section_id);
        $this->assertEquals(1, $remember_page->page);
        $this->assertEquals($book->characters_count, $remember_page->characters_count);
    }

    public function testStopReadingHttp()
    {
        $book = Book::factory()->with_create_user()->with_section()->create();

        $section = $book->sections()->first();

        $response = $this->actingAs($book->create_user)
            ->get(route('books.sections.show', ['book' => $book->id, 'section' => $section->inner_id]))
            ->assertOk();

        $this->assertNotNull($book->create_user->remembered_pages()->first());

        $response = $this->actingAs($book->create_user)
            ->get(route('books.stop_reading', ['book' => $book->id, 'section' => $section->inner_id]))
            ->assertOk();

        $book->fresh();

        $this->assertNull($book->remembered_pages()->first());
    }

    public function testBooksCountWithNewText()
    {
        $user = User::factory()->create();

        $book = Book::factory()->with_section()->create()
            ->fresh();

        $book_read_remember_page = BookReadRememberPage::factory()->create([
            'book_id' => $book->id,
            'user_id' => $user->id,
            'characters_count' => ($book->characters_count - 10)
        ]);

        $user_book = UserBook::factory()->create([
            'book_id' => $book->id,
            'user_id' => $user->id,
        ]);

        $this->assertTrue($book->characters_count > $book_read_remember_page->characters_count);
        $this->assertEquals($book_read_remember_page->user_id, $user->id);
        $this->assertEquals($book_read_remember_page->book_id, $book->id);

        $this->assertEquals(1, $user->getFavoriteBooksWithUpdatesCount());

        $book_read_remember_page->characters_count = $book->characters_count;
        $book_read_remember_page->save();

        $user->flushCachedFavoriteBooksWithUpdatesCount();

        $this->assertEquals(0, $user->getFavoriteBooksWithUpdatesCount());
    }

    public function testFlushCacheFavoriteBooksWithUpdatesCountAfterBookPageViewed()
    {
        $user = User::factory()->create();

        $book = Book::factory()->with_section()->create()
            ->fresh();

        $book_read_remember_page = BookReadRememberPage::factory()->create([
            'book_id' => $book->id,
            'user_id' => $user->id,
            'characters_count' => ($book->characters_count - 10)
        ]);

        $user_book = UserBook::factory()->create([
            'book_id' => $book->id,
            'user_id' => $user->id,
        ]);

        $this->assertEquals(1, $user->getFavoriteBooksWithUpdatesCount());

        $book->rememberPageForUser($user, $book_read_remember_page->page);

        $this->assertEquals(0, $user->getFavoriteBooksWithUpdatesCount());
    }

    public function testBooksCountWithNewTextAndOtherUser()
    {
        $user = User::factory()->create();

        $book = Book::factory()->with_section()->create()
            ->fresh();

        $book_read_remember_page = BookReadRememberPage::factory()->create([
            'book_id' => $book->id,
            'user_id' => $user->id,
            'characters_count' => ($book->characters_count - 10)
        ]);

        $book_read_remember_page2 = BookReadRememberPage::factory()->create([
            'book_id' => $book->id,
            'characters_count' => ($book->characters_count - 10)
        ]);

        $user_book = UserBook::factory()->create([
            'book_id' => $book->id,
            'user_id' => $user->id,
        ]);

        $this->assertEquals(1, $user->getFavoriteBooksWithUpdatesCount());

        $book_read_remember_page->characters_count = $book->characters_count;
        $book_read_remember_page->save();

        $user->flushCachedFavoriteBooksWithUpdatesCount();

        $this->assertEquals(0, $user->getFavoriteBooksWithUpdatesCount());
    }

    public function testBookUpdatesCounterIfCharacterCountChange()
    {
        $user = User::factory()->create();

        $book = Book::factory()->with_section()->create()
            ->fresh();

        $book_read_remember_page = BookReadRememberPage::factory()->create([
            'book_id' => $book->id,
            'user_id' => $user->id,
            'characters_count' => $book->characters_count
        ]);

        $user_book = UserBook::factory()->create([
            'book_id' => $book->id,
            'user_id' => $user->id,
        ]);

        $this->assertEquals(0, $user->getFavoriteBooksWithUpdatesCount());

        $book->sections->first()->characters_count = ($book->characters_count + 100);
        $book->push();

        $book->refreshCharactersCount();

        $this->assertEquals(1, $user->getFavoriteBooksWithUpdatesCount());
    }

    public function testAligmentChractersCountCommand()
    {
        $book_read_remember_page = BookReadRememberPage::factory()->create([
            'characters_count' => 0
        ]);

        $book = $book_read_remember_page->book;
        $book->characters_count = rand(100, 1000);
        $book->save();
        $book->refresh();

        $book_read_remember_page2 = BookReadRememberPage::factory()->create([
            'characters_count' => 0
        ]);

        $book2 = $book_read_remember_page2->book;
        $book2->characters_count = rand(100, 1000);
        $book2->save();
        $book2->refresh();

        $book_read_remember_page3 = BookReadRememberPage::factory()->create([
            'book_id' => $book2->id,
            'characters_count' => 0
        ]);

        Artisan::call('to_new:aligment_characters_count_with_remembered_page', ['start_id' => $book->id]);

        $book_read_remember_page = BookReadRememberPage::where('book_id', $book->id)->where('user_id', $book_read_remember_page->user_id)->first();
        $book_read_remember_page2 = BookReadRememberPage::where('book_id', $book2->id)->where('user_id', $book_read_remember_page2->user_id)->first();
        $book_read_remember_page3 = BookReadRememberPage::where('book_id', $book2->id)->where('user_id', $book_read_remember_page3->user_id)->first();
        /*
                dump($book->characters_count);
                dump($book_read_remember_page);
                dump($book_read_remember_page3);
        */
        $this->assertEquals($book->characters_count, $book_read_remember_page->characters_count);
        $this->assertEquals($book2->characters_count, $book_read_remember_page2->characters_count);
        $this->assertEquals($book2->characters_count, $book_read_remember_page3->characters_count);
    }

    public function testBookListHttpOk()
    {
        $book = Book::factory()->with_three_sections()->with_create_user()->create();

        $user = $book->create_user;
        $section = $book->sections()->first();

        $book_read_remember_page = BookReadRememberPage::factory()->create([
            'book_id' => $book->id,
            'user_id' => $user->id,
            'inner_section_id' => $section->inner_id,
            'characters_count' => 0
        ]);

        $this->actingAs($user)
            ->get(route('users.books.created', ['user' => $user]))
            ->assertOk();
    }

    public function testRememberThePageFromTheBeginningOfTheBook()
    {
        $user = User::factory()->create();

        $book = Book::factory()->with_create_user()->create();

        $chapter1 = Section::factory()->with_two_pages()->create(['book_id' => $book->id]);

        $chapter2 = Section::factory()->with_two_pages()->create(['book_id' => $book->id]);

        BookUpdatePageNumbersJob::dispatch($book);

        $this->get(route('books.pages', ['book' => $book, 'page' => 2]))
            ->assertRedirect(route('books.sections.show', [
                'book' => $book,
                'section' => $chapter1->inner_id,
                'page' => 2
            ]));

        $this->get(route('books.pages', ['book' => $book, 'page' => 4]))
            ->assertRedirect(route('books.sections.show', [
                'book' => $book,
                'section' => $chapter2->inner_id,
                'page' => 2
            ]));

        $response = $this->actingAs($book->create_user)
            ->followingRedirects()
            ->get(route('books.pages', ['book' => $book->id, 'page' => 4]))
            ->assertOk();

        $remember_page = $book->create_user->remembered_pages()->first();

        $this->assertEquals(2, $remember_page->page);
    }
}
