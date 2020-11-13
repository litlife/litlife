<?php

namespace Tests\Feature\Book;

use App\Author;
use App\Book;
use App\BookFile;
use App\CollectedBook;
use App\Comment;
use App\Jobs\Book\UpdateBookFilesCount;
use App\Section;
use App\User;
use App\UserPurchase;
use Tests\TestCase;

class BookShowTest extends TestCase
{
    public function testViewBookIfUserBuyThisBook()
    {
        $book = Book::factory()->create();

        $book->delete();

        $reader = User::factory()->create();

        $purchase = UserPurchase::factory()->create([
            'buyer_user_id' => $reader->id,
            'purchasable_type' => 'book',
            'purchasable_id' => $book->id,
        ]);

        $this->actingAs($reader)
            ->get(route('books.show', $book))
            ->assertOk()
            ->assertDontSeeText($book->title)
            ->assertSeeText(__('Book was deleted'));
    }

    public function testSeeTextRemoveFromSaleHttp()
    {
        $book = Book::factory()->removed_from_sale()->create();

        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('books.show', ['book' => $book]))
            ->assertOk()
            ->assertSeeText(__('book.removed_from_sale'));
    }

    public function testSeePrivateAuthorIfBookSentOnReview()
    {
        $book = Book::factory()->sent_for_review()->create();

        $author = Author::factory()->private()->create();

        $book->authors()->sync([$author->id]);

        $user = $author->create_user;

        $this->actingAs($user)
            ->get(route('books.show', $book))
            ->assertOk()
            ->assertSeeText($author->name);

        $other_user = User::factory()->create();

        $this->actingAs($other_user)
            ->get(route('books.show', $book))
            ->assertOk()
            ->assertDontSeeText($author->name);
    }

    public function testShowSentForReviewOk()
    {
        $user = User::factory()->create();

        $book = Book::factory()->sent_for_review()->create();
        $book->status_changed_user_id = $user->id;
        $book->save();

        $this->assertTrue($book->isSentForReview());

        $this->get(route('books.show', $book))
            ->assertOk();
    }

    public function testShowPrivateBook()
    {
        $book = Book::factory()->private()->with_create_user()->create();

        $this->get(route('books.show', $book))
            ->assertForbidden()
            ->assertSeeText(__('book.access_denied'));
    }

    public function testShowSentForReviewBook()
    {
        $book = Book::factory()->sent_for_review()->create();

        $this->get(route('books.show', $book))
            ->assertOk()
            ->assertSeeText(__('book.on_check'));
    }

    public function testGuestSeeOnReview()
    {
        $book = Book::factory()->sent_for_review()->create();

        $this->assertTrue($book->isSentForReview());

        $response = $this->get(route('books.show', $book))
            ->assertOk()
            ->assertSeeText($book->title)
            ->assertSeeText(__('book.on_check'))
            ->assertDontSeeText(__('book.you_will_receive_a_notification_when_the_book_is_published'))
            ->assertDontSeeText(__('book.added_for_check'));
    }

    public function testViewFilesOnReviewIfBookOnReview()
    {
        foreach (BookFile::sentOnReview()->get() as $file) {
            $file->delete();
        }

        $book = Book::factory()->create();
        $book->statusSentForReview();
        $book->save();
        $book->refresh();

        $book_file = BookFile::factory()->txt()->create(['book_id' => $book->id]);
        $book_file->statusSentForReview();
        $book_file->save();
        UpdateBookFilesCount::dispatch($book);
        $book->refresh();

        $this->get(route('books.show', $book))
            ->assertOk()
            ->assertDontSeeText($book_file->extension);

        $admin = User::factory()->with_user_group()->create();
        $admin->group->book_file_add_check = true;
        $admin->push();

        $user = User::factory()->create();

        $this->actingAs($admin)
            ->get(route('books.show', $book))
            ->assertOk()
            ->assertSeeText($book_file->extension);

        $this->actingAs($user)
            ->get(route('books.show', $book))
            ->assertOk()
            ->assertDontSeeText($book_file->extension);
    }

    public function testIfCommentOnReview()
    {
        $comment = Comment::factory()
            ->book()
            ->sent_for_review()
            ->create();

        $user = User::factory()
            ->create();

        $this->assertTrue($comment->isSentForReview());

        $this->actingAs($comment->create_user)
            ->get(route('books.show', $comment->commentable->id))
            ->assertOk()
            ->assertSeeText($comment->text);

        $this->actingAs($user)
            ->get(route('books.show', $comment->commentable->id))
            ->assertOk()
            ->assertDontSeeText($comment->text)
            ->assertSeeText(trans_choice('comment.on_check', 1));
    }

    public function testIsOkIfBookDeleted()
    {
        $comment = Comment::factory()->create();

        $this->assertTrue($comment->isBookType());

        $book = $comment->commentable;

        $this->get(route('books.show', $book))
            ->assertOk();

        $comment->commentable->delete();

        $this->get(route('books.show', $book))
            ->assertNotFound();
    }

    public function testViewCounterIncrement()
    {
        $section = Section::factory()->create();

        $book = $section->book;
        $book->statusAccepted();
        $book->push();
        $book->refresh();

        $this->assertEquals(0, $book->view_count->day);
        $this->assertEquals(0, $book->view_count->week);
        $this->assertEquals(0, $book->view_count->month);
        $this->assertEquals(0, $book->view_count->year);
        $this->assertEquals(0, $book->view_count->all);

        $this->get(route('books.show', $book))
            ->assertOk();

        $book->refresh();

        $this->assertEquals(1, $book->view_count->day);
        $this->assertEquals(1, $book->view_count->week);
        $this->assertEquals(1, $book->view_count->month);
        $this->assertEquals(1, $book->view_count->year);
        $this->assertEquals(1, $book->view_count->all);

        $this->get(route('books.show', $book))
            ->assertOk();

        $book->refresh();

        $this->assertEquals(1, $book->view_count->day);
        $this->assertEquals(1, $book->view_count->week);
        $this->assertEquals(1, $book->view_count->month);
        $this->assertEquals(1, $book->view_count->year);
        $this->assertEquals(1, $book->view_count->all);

        $this->get(route('books.show', $book), ['REMOTE_ADDR' => $this->faker->ipv4])
            ->assertOk();

        $book->refresh();

        $this->assertEquals(2, $book->view_count->day);
        $this->assertEquals(2, $book->view_count->week);
        $this->assertEquals(2, $book->view_count->month);
        $this->assertEquals(2, $book->view_count->year);
        $this->assertEquals(2, $book->view_count->all);

        $this->get(route('books.sections.show', ['book' => $book, 'section' => $section->inner_id]), ['REMOTE_ADDR' => $this->faker->ipv4])
            ->assertOk();

        $book->refresh();

        $this->assertEquals(3, $book->view_count->day);
        $this->assertEquals(3, $book->view_count->week);
        $this->assertEquals(3, $book->view_count->month);
        $this->assertEquals(3, $book->view_count->year);
        $this->assertEquals(3, $book->view_count->all);
    }

    public function testInCollection()
    {
        $collectedBook = CollectedBook::factory()->create();

        $book = $collectedBook->book;
        $collection = $collectedBook->collection;

        $this->get(route('books.show', $book))
            ->assertOk()
            ->assertViewHas('collectionsCount', 1);
    }
}
