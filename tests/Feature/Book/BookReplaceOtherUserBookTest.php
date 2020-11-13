<?php

namespace Tests\Feature\Book;

use App\Author;
use App\Book;
use App\BookGroup;
use Tests\TestCase;

class BookReplaceOtherUserBookTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testReplace()
    {
        $author = Author::factory()->with_author_manager()->create();

        $manager = $author->managers()->first();
        $user = $manager->user;

        $book = Book::factory()->accepted()->create(['create_user_id' => $user->id]);

        $book2 = Book::factory()->accepted()->create();

        $book->writers()->sync([$author->id]);
        $book2->writers()->sync([$author->id]);

        $this->actingAs($user)
            ->get(route('books.replace_book_created_by_another_user.form', ['book' => $book]))
            ->assertOk()
            ->assertSeeText(__('book.replace_book_created_by_another_user_helper'));

        $this->actingAs($user)
            ->post(route('books.replace_book_created_by_another_user', ['book' => $book]),
                ['book_id' => $book2->id])
            ->assertRedirect(route('books.show', $book))
            ->assertSessionHas(['success' => __('book.you_have_successfully_replaced_your_book_with_a_book_added_by_another_user')]);

        $book->refresh();
        $book2->refresh();

        $this->assertTrue($book->isReadAccess());
        $this->assertTrue($book->isDownloadAccess());

        $this->assertFalse($book2->isReadAccess());
        $this->assertFalse($book2->isDownloadAccess());

        $this->assertTrue($book->isInGroup());
        $this->assertTrue($book2->isInGroup());

        $this->assertTrue($book->isMainInGroup());
        $this->assertFalse($book2->isMainInGroup());

        $this->assertEquals($book->id, $book2->main_book_id);
        $this->assertEquals(1, $book->editions_count);
        $this->assertEquals(1, $book2->editions_count);
    }

    public function testSeeIdMustNotMatchError()
    {
        $author = Author::factory()->with_author_manager()->create();

        $manager = $author->managers()->first();
        $user = $manager->user;

        $book = Book::factory()->accepted()->create(['create_user_id' => $user->id]);

        $book->writers()->sync([$author->id]);

        $this->actingAs($user)
            ->post(route('books.replace_book_created_by_another_user', ['book' => $book]),
                ['book_id' => $book->id])
            ->assertRedirect()
            ->assertSessionHasErrors(['book_id' => __('book.id_of_the_book_being_replaced_must_not_match_the_id_of_the_book_being_replaced')]);
    }

    public function testSeeMustAddedByAnotherUserError()
    {
        $author = Author::factory()->with_author_manager()->create();

        $manager = $author->managers()->first();
        $user = $manager->user;

        $book = Book::factory()->accepted()->create(['create_user_id' => $user->id]);

        $book2 = Book::factory()->accepted()->create(['create_user_id' => $user->id]);

        $book->writers()->sync([$author->id]);
        $book2->writers()->sync([$author->id]);

        $this->actingAs($user)
            ->post(route('books.replace_book_created_by_another_user', ['book' => $book]),
                ['book_id' => $book2->id])
            ->assertRedirect()
            ->assertSessionHasErrors(['book_id' => __('book.enter_the_id_of_the_book_that_another_user_added')]);
    }

    public function testSeeMustBelongsToYourAuthorPageError()
    {
        $author = Author::factory()->with_author_manager()->create();

        $manager = $author->managers()->first();
        $user = $manager->user;

        $book = Book::factory()->accepted()->create(['create_user_id' => $user->id]);

        $book2 = Book::factory()->accepted()->create();

        $author2 = Author::factory()->create();

        $book->writers()->sync([$author->id]);
        $book2->writers()->sync([$author2->id]);

        $this->actingAs($user)
            ->post(route('books.replace_book_created_by_another_user', ['book' => $book]),
                ['book_id' => $book2->id])
            ->assertRedirect()
            ->assertSessionHasErrors(['book_id' => __('book.enter_the_id_of_the_book_that_belongs_to_your_author_page')]);
    }

    public function testPolicy()
    {
        $author = Author::factory()->with_author_manager()->create();

        $manager = $author->managers()->first();
        $user = $manager->user;

        $book = Book::factory()->accepted()->create(['create_user_id' => $user->id]);

        $book2 = Book::factory()->accepted()->create();

        $book->writers()->sync([$author->id]);
        $book2->writers()->sync([$author->id]);

        $book->refresh();
        $book2->refresh();

        $this->assertTrue($user->can('replaceWithThis', $book));
        $this->assertTrue($user->can('replaceThis', $book2));

        $this->assertFalse($user->can('replaceThis', $book));
        $this->assertFalse($user->can('replaceWithThis', $book2));
    }

    public function testPolicyCantReplaceWithThisIfBookMainInGroup()
    {
        $author = Author::factory()->with_author_manager()->create();

        $manager = $author->managers()->first();
        $user = $manager->user;

        $book = Book::factory()->accepted()->create(['create_user_id' => $user->id]);

        $group = BookGroup::factory()->create();
        $book->addToGroup($group, true);
        $book->save();
        $book->refresh();

        $this->assertFalse($user->can('replaceWithThis', $book));
    }

    public function testAttachThirdBook()
    {
        $author = Author::factory()->with_author_manager()->create();

        $manager = $author->managers()->first();
        $user = $manager->user;

        $book = Book::factory()->accepted()->create(['create_user_id' => $user->id]);

        $book2 = Book::factory()->accepted()->create();

        $book3 = Book::factory()->accepted()->create();

        $book->writers()->sync([$author->id]);
        $book2->writers()->sync([$author->id]);
        $book3->writers()->sync([$author->id]);

        $this->actingAs($user)
            ->post(route('books.replace_book_created_by_another_user', ['book' => $book]),
                ['book_id' => $book2->id])
            ->assertRedirect(route('books.show', $book))
            ->assertSessionHas(['success' => __('book.you_have_successfully_replaced_your_book_with_a_book_added_by_another_user')]);

        $this->actingAs($user)
            ->post(route('books.replace_book_created_by_another_user', ['book' => $book]),
                ['book_id' => $book3->id])
            ->assertRedirect(route('books.show', $book))
            ->assertSessionHas(['success' => __('book.you_have_successfully_replaced_your_book_with_a_book_added_by_another_user')]);

        $book->refresh();
        $book2->refresh();
        $book3->refresh();

        $this->assertTrue($book->isReadAccess());
        $this->assertTrue($book->isDownloadAccess());

        $this->assertFalse($book2->isReadAccess());
        $this->assertFalse($book2->isDownloadAccess());

        $this->assertFalse($book3->isReadAccess());
        $this->assertFalse($book3->isDownloadAccess());

        $this->assertTrue($book->isInGroup());
        $this->assertTrue($book2->isInGroup());
        $this->assertTrue($book3->isInGroup());

        $this->assertTrue($book->isMainInGroup());
        $this->assertFalse($book2->isMainInGroup());
        $this->assertFalse($book3->isMainInGroup());

        $this->assertEquals($book->id, $book2->main_book_id);
        $this->assertEquals($book->id, $book3->main_book_id);
    }
}
