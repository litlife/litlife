<?php

namespace Tests\Feature\Book\Section;

use App\Author;
use App\Book;
use App\Section;
use App\User;
use App\UserPurchase;
use Tests\TestCase;

class SectionShowTest extends TestCase
{
    public function testViewSectionIfBookPurchasedHttp()
    {
        $book = Book::factory()->with_section()->create();

        $book->delete();

        $section = $book->sections()->first();

        $reader = User::factory()->create();

        $purchase = UserPurchase::factory()->create([
            'buyer_user_id' => $reader->id,
            'purchasable_type' => 'book',
            'purchasable_id' => $section->book->id,
        ]);

        $this->assertTrue($reader->can('view', $section));

        $this->actingAs($reader)
            ->get(route('books.sections.show', ['book' => $book, 'section' => $section->inner_id]))
            ->assertOk();
    }

    public function testViewSectionIfBookNotPurchasedAndBookDeletedHttp()
    {
        $book = Book::factory()->with_section()->create();

        $book->delete();

        $section = $book->sections()->first();

        $reader = User::factory()->create();

        $this->assertFalse($reader->can('view', $section));

        $this->actingAs($reader)
            ->get(route('books.sections.show', ['book' => $book, 'section' => $section->inner_id]))
            ->assertForbidden()
            ->assertSeeText(__('book.book_deleted'));
    }

    public function testViewSectionIfItIsAPrivate()
    {
        $author = Author::factory()->with_author_manager()->with_book()->create();

        $user = $author->managers->first()->user;
        $book = $author->books->first();
        $book->create_user()->associate($user);
        $book->push();

        $section = $book->sections()->first();
        $section->statusPrivate();
        $section->push();

        $this->actingAs($user)
            ->get(route('books.sections.show', ['book' => $book, 'section' => $section->inner_id]))
            ->assertOk();

        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('books.sections.show', ['book' => $book, 'section' => $section->inner_id]))
            ->assertForbidden()
            ->assertSeeText(__('section.access_is_limited'));

        $this->get(route('books.sections.show', ['book' => $book, 'section' => $section->inner_id]))
            ->assertForbidden()
            ->assertSeeText(__('section.access_is_limited'));
    }

    public function testViewSectionIfBookPurchasedAndFreeSectionsHttp()
    {
        $author = Author::factory()->with_book_for_sale()->with_author_manager_can_sell()->create();

        $book = $author->any_books()->first();
        $book->free_sections_count = 1;
        $book->save();

        $section = $book->sections()->defaultOrder()->first();

        $user = User::factory()->create();

        $section2 = Section::factory()->create(['book_id' => $book->id, 'inner_id' => 3]);

        $this->get(route('books.sections.show', ['book' => $book, 'section' => $section->inner_id]))
            ->assertOk();

        $this->followingRedirects()
            ->get(route('books.sections.show', ['book' => $book, 'section' => $section2->inner_id]))
            ->assertStatus(401);

        $this->actingAs($user)
            ->get(route('books.sections.show', ['book' => $book, 'section' => $section2->inner_id]))
            ->assertRedirect(route('books.purchase', $book))
            ->assertSessionHas(['info' => __('book.paid_part_of_book')]);

        $this->actingAs($user)
            ->followingRedirects()
            ->get(route('books.sections.show', ['book' => $book, 'section' => $section2->inner_id]))
            ->assertOk()
            ->assertSeeText(__('book.paid_part_of_book'));
    }

    public function testViewIfBookPrivate()
    {
        $book = Book::factory()->private()->with_section()->with_create_user()->create();

        $section = $book->sections()->first();
        $user = $book->create_user;

        $this->assertTrue($book->isPrivate());
        $this->assertTrue($user->can('view', $section));

        $this->actingAs($user)
            ->get(route('books.sections.show', ['book' => $book, 'section' => $section->inner_id]))
            ->assertOk();
    }

    public function testViewIfSectionPrivateAndBookPrivate()
    {
        $section = Section::factory()->private()->book_private()->create();

        $book = $section->book;
        $user = $section->book->create_user;

        $this->assertTrue($book->isPrivate());
        $this->assertTrue($section->isPrivate());
        $this->assertTrue($user->can('view', $section));

        $this->actingAs($user)
            ->get(route('books.sections.show', ['book' => $book, 'section' => $section->inner_id]))
            ->assertOk();
    }

    public function testShowRouteIsOkIfNoPagesIfPageFirst()
    {
        $section = Section::factory()->no_pages()->create();

        $section->book->statusAccepted();
        $section->push();

        $this->assertEquals(0, $section->pages()->count());
        $this->assertTrue($section->book->isAccepted());

        $this->get(route('books.sections.show', ['book' => $section->book, 'section' => $section->inner_id]))
            ->assertOk();

        $this->get(route('books.sections.show', ['book' => $section->book, 'section' => $section->inner_id, 'page' => 1]))
            ->assertOk();

        $this->get(route('books.sections.show', ['book' => $section->book, 'section' => $section->inner_id, 'page' => 2]))
            ->assertNotFound();
    }

    public function testShowNotFound()
    {
        $section = Section::factory()->create();

        $book = $section->book;

        $this->get(route('books.sections.show', ['book' => $book, 'section' => $section->inner_id + 1]))
            ->assertNotFound();
    }

    public function testCantViewPrivateBookSectionText()
    {
        $book = Book::factory()->private()->with_section()->create();

        $section = $book->sections()->chapter()->first();

        $this->get(route('books.sections.show', ['book' => $book, 'section' => $section->inner_id]))
            ->assertForbidden();
    }

    public function testCanViewSectionTextIfBookPublished()
    {
        $book = Book::factory()->accepted()->with_section()->create();

        $section = $book->sections()->chapter()->first();

        $this->get(route('books.sections.show', ['book' => $book, 'section' => $section->inner_id]))
            ->assertOk();
    }

    public function testSectionShowRouteIsNotFoundIfPageNotExists()
    {
        $book = Book::factory()->accepted()->with_section()->create();

        $section = $book->sections()->chapter()->first();

        $this->get(route('books.sections.show', [
            'book' => $book,
            'section' => $section->inner_id,
            'page' => 1234
        ]))
            ->assertNotFound()
            ->assertSeeText(__('section.book_page_was_not_found'))
            ->assertSeeText(__('section.go_to_the_sections_index'));
    }

    public function testNotFound()
    {
        $book = Book::factory()->accepted()->create();

        $this->get(route('books.sections.show', ['book' => $book, 'section' => rand(100, 1000)]))
            ->assertNotFound();
    }
}
