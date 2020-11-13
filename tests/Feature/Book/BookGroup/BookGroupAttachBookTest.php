<?php

namespace Tests\Feature\Book\BookGroup;

use App\Book;
use App\BookKeyword;
use App\BookStatus;
use App\BookVote;
use App\Comment;
use App\Jobs\Book\BookGroupJob;
use App\Keyword;
use App\User;
use Tests\TestCase;

class BookGroupAttachBookTest extends TestCase
{
    public function testAttachBook()
    {
        $user = User::factory()->create();

        $book = Book::factory()->create();
        $attachable_book = Book::factory()->create();

        $this->actingAs($user);

        BookGroupJob::dispatch($book, $attachable_book);

        $book->refresh();
        $attachable_book->refresh();

        $this->assertNull($book->main_book_id);
        $this->assertEquals($book->id, $attachable_book->main_book_id);
        $this->assertEquals(1, $book->editions_count);
        $this->assertEquals(1, $attachable_book->editions_count);
        $this->assertNotNull($attachable_book->connected_at);
        $this->assertEquals($user->id, $attachable_book->connect_user_id);
    }

    public function testAttachBookWithStatus()
    {
        $book = Book::factory()->create();

        $status = BookStatus::factory()->create(['status' => 'readed']);

        $attachable_book = $status->book;

        BookGroupJob::dispatch($book, $attachable_book);

        $status->refresh();

        $this->assertEquals($status->book_id, $book->id);
        $this->assertEquals($status->origin_book_id, $attachable_book->id);
    }

    public function testAttachBookWithVote()
    {
        $book = Book::factory()->create();

        $vote = BookVote::factory()->create(['vote' => 7]);

        $attachable_book = $vote->book;

        BookGroupJob::dispatch($book, $attachable_book);

        $vote->refresh();

        $this->assertEquals($vote->book_id, $book->id);
        $this->assertEquals($vote->origin_book_id, $attachable_book->id);
    }

    public function testAttachedBookAttachToAnotherBook()
    {
        $mainBook = Book::factory()->create();

        $minorBook = Book::factory()->create(['main_book_id' => $mainBook->id]);

        $newMainBook = Book::factory()->create();

        BookGroupJob::dispatch($newMainBook, $minorBook);

        $this->assertEquals($newMainBook->id, $minorBook->main_book_id);
        $this->assertEquals(0, $mainBook->groupedBooks()->count());
        $this->assertEquals(1, $newMainBook->groupedBooks()->count());
    }

    public function testAttachBookWithStatusAndSameUser()
    {
        $user = User::factory()->create();

        $mainBook = Book::factory()->create();

        $minorBook = Book::factory()->create();

        $user_updated_at = now()->subDays();

        $status = BookStatus::factory()->create([
            'status' => 'readed',
            'book_id' => $mainBook->id,
            'user_id' => $user->id,
            'user_updated_at' => $user_updated_at
        ]);

        $status2 = BookStatus::factory()->create([
            'status' => 'read_later',
            'book_id' => $minorBook->id,
            'user_id' => $user->id
        ]);

        BookGroupJob::dispatch($mainBook, $minorBook);

        $status->refresh();

        $this->assertEquals($status->book_id, $mainBook->id);
        $this->assertEquals($user_updated_at->timestamp, $status->user_updated_at->timestamp);

        $this->assertDatabaseMissing('book_statuses', [
            'id' => $status2->id
        ]);
    }

    public function testAttachBookWithTwoStatuses()
    {
        $mainBook = Book::factory()->create();

        $minorBook = Book::factory()->create();

        $status = BookStatus::factory()->create([
            'status' => 'readed',
            'book_id' => $mainBook->id
        ]);

        $status2 = BookStatus::factory()->create([
            'status' => 'read_now',
            'book_id' => $minorBook->id
        ]);

        $mainBook->refresh();
        $minorBook->refresh();

        $this->assertEquals(1, $mainBook->user_read_count);
        $this->assertEquals(0, $mainBook->user_read_now_count);

        $this->assertEquals(0, $minorBook->user_read_count);
        $this->assertEquals(1, $minorBook->user_read_now_count);

        $this->assertEquals($mainBook->id, $status->book_id);
        $this->assertEquals($minorBook->id, $status2->book_id);

        BookGroupJob::dispatch($mainBook, $minorBook);

        $mainBook->refresh();
        $status->refresh();
        $status2->refresh();

        $this->assertEquals($mainBook->id, $status->book_id);
        $this->assertEquals($mainBook->id, $status2->book_id);
        $this->assertEquals($mainBook->id, $status->origin_book_id);

        $this->assertEquals(2, $mainBook->users_read_statuses()->count());

        $this->assertEquals(1, $mainBook->user_read_count);
        $this->assertEquals(1, $mainBook->user_read_now_count);
    }

    public function testAttachBookWithVoteAndSameUser()
    {
        $user = User::factory()->create();

        $mainBook = Book::factory()->create();

        $minorBook = Book::factory()->create();

        $user_updated_at = now()->subDays();

        $vote = BookVote::factory()->create([
            'book_id' => $mainBook->id,
            'create_user_id' => $user->id,
            'user_updated_at' => $user_updated_at
        ]);

        $vote2 = BookVote::factory()->create([
            'book_id' => $minorBook->id,
            'create_user_id' => $user->id,
            'user_updated_at' => $user_updated_at
        ]);

        BookGroupJob::dispatch($mainBook, $minorBook);

        $vote->refresh();
        $vote2->refresh();
        $user->refresh();

        $this->assertEquals($mainBook->id, $vote->book_id);
        $this->assertEquals($mainBook->id, $vote->origin_book_id);
        $this->assertFalse($vote->trashed());
        $this->assertEquals($user->id, $vote->create_user_id);
        $this->assertEquals($user_updated_at->timestamp, $vote->user_updated_at->timestamp);

        $this->assertEquals($minorBook->id, $vote2->book_id);
        $this->assertEquals($minorBook->id, $vote2->origin_book_id);
        $this->assertTrue($vote2->trashed());
        $this->assertEquals($user->id, $vote2->create_user_id);
        $this->assertEquals($user_updated_at->timestamp, $vote2->user_updated_at->timestamp);

        $this->assertEquals(1, $user->book_rate_count);
    }

    public function testAttachBookWithTwoVotes()
    {
        $mainBook = Book::factory()->create();

        $minorBook = Book::factory()->create();

        $vote = BookVote::factory()->create(['book_id' => $mainBook->id, 'vote' => 6]);

        $vote2 = BookVote::factory()->create(['book_id' => $minorBook->id, 'vote' => 4]);

        BookGroupJob::dispatch($mainBook, $minorBook);

        $mainBook->refresh();
        $vote->refresh();
        $vote2->refresh();

        $this->assertEquals($mainBook->id, $vote->book_id);
        $this->assertEquals($mainBook->id, $vote2->book_id);
        $this->assertEquals($mainBook->id, $vote->origin_book_id);

        $this->assertEquals(2, $mainBook->user_vote_count);
        $this->assertEquals(5, $mainBook->vote_average);
    }

    public function testAttachBookWithTwoBookKeywords()
    {
        $mainBook = Book::factory()->create();

        $minorBook = Book::factory()->create();

        $bookKeyword = BookKeyword::factory()->create(['book_id' => $mainBook->id]);

        $bookKeyword2 = BookKeyword::factory()->create(['book_id' => $minorBook->id]);

        BookGroupJob::dispatch($mainBook, $minorBook);

        $mainBook->refresh();
        $bookKeyword->refresh();
        $bookKeyword2->refresh();

        $this->assertEquals($mainBook->id, $bookKeyword->book_id);
        $this->assertEquals($mainBook->id, $bookKeyword2->book_id);
        $this->assertEquals($minorBook->id, $bookKeyword2->origin_book_id);
        $this->assertEquals($mainBook->id, $bookKeyword->origin_book_id);

        $this->assertEquals(2, $mainBook->book_keywords()->count());
    }

    public function testAttachBookWithSameKeywords()
    {
        $mainBook = Book::factory()->create();

        $minorBook = Book::factory()->create();

        $keyword = Keyword::factory()->create();

        $bookKeyword = BookKeyword::factory()->create(['book_id' => $mainBook->id, 'keyword_id' => $keyword->id]);

        $bookKeyword2 = BookKeyword::factory()->create(['book_id' => $minorBook->id, 'keyword_id' => $keyword->id]);

        BookGroupJob::dispatch($mainBook, $minorBook);

        $mainBook->refresh();
        $bookKeyword->refresh();
        $bookKeyword2->refresh();

        $this->assertEquals($mainBook->id, $bookKeyword->book_id);
        $this->assertEquals($minorBook->id, $bookKeyword2->book_id);

        $this->assertEquals(1, $mainBook->book_keywords()->count());
    }

    public function testAttachBookWithSoftDeletedVote()
    {
        $user = User::factory()->create();

        $mainBook = Book::factory()->create();

        $minorBook = Book::factory()->create();

        $vote = BookVote::factory()->create(['book_id' => $mainBook->id, 'vote' => 6, 'create_user_id' => $user->id]);

        $vote->delete();

        $vote2 = BookVote::factory()->create(['book_id' => $minorBook->id, 'vote' => 4, 'create_user_id' => $user->id]);

        $this->assertSoftDeleted($vote);

        BookGroupJob::dispatch($mainBook, $minorBook);

        $mainBook->refresh();
        $vote2->refresh();

        $this->assertEquals($mainBook->id, $vote2->book_id);
        $this->assertEquals(1, $mainBook->user_vote_count);
    }

    public function testAttachBookWithZeroStatus()
    {
        $user = User::factory()->create();

        $mainBook = Book::factory()->create();

        $minorBook = Book::factory()->create();

        $status = BookStatus::factory()->create(['book_id' => $mainBook->id, 'user_id' => $user->id, 'status' => 'null']);

        $status2 = BookStatus::factory()->create(['book_id' => $minorBook->id, 'user_id' => $user->id, 'status' => 'readed']);

        BookGroupJob::dispatch($mainBook, $minorBook);

        $mainBook->refresh();
        $status2->refresh();

        $this->assertEquals($mainBook->id, $status2->book_id);
    }

    public function testAttachBookWithSameKeywordsOneSoftDeleted()
    {
        $mainBook = Book::factory()->create();

        $minorBook = Book::factory()->create();

        $keyword = Keyword::factory()->create();

        $bookKeyword = BookKeyword::factory()->create(['book_id' => $mainBook->id, 'keyword_id' => $keyword->id]);

        $bookKeyword2 = BookKeyword::factory()->create(['book_id' => $minorBook->id, 'keyword_id' => $keyword->id]);

        $bookKeyword->delete();

        BookGroupJob::dispatch($mainBook, $minorBook);

        $bookKeyword2->refresh();

        $this->assertEquals($mainBook->id, $bookKeyword2->book_id);

        $this->assertEquals(1, $mainBook->book_keywords()->count());
    }

    public function testUpdateInfoFalse()
    {
        $mainBook = Book::factory()->create();

        $minorBook = Book::factory()->create();

        BookGroupJob::dispatch($mainBook, $minorBook, false);

        $mainBook->refresh();
        $minorBook->refresh();

        $this->assertTrue($mainBook->isInGroup());
        $this->assertTrue($minorBook->isInGroup());

        $this->assertNull($mainBook->connected_at);
        $this->assertNull($mainBook->connect_user_id);

        $this->assertNull($minorBook->connected_at);
        $this->assertNull($minorBook->connect_user_id);

        $this->assertEquals(0, $mainBook->activities()->count());
        $this->assertEquals(0, $minorBook->activities()->count());
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testAttachBookInGroupToBookNotInGroup()
    {
        $user = User::factory()->create()->fresh();
        $user->group->connect_books = true;
        $user->push();

        $book = Book::factory()->accepted()->create();

        $book2 = Book::factory()->accepted()->create();

        $response = $this->actingAs($user)
            ->followingRedirects()
            ->post(route('books.group.attach', $book2), ['edition_id' => $book->id], [
                'HTTP_REFERER' => route('books.editions.edit', ['book' => $book2])
            ]);
        $response->assertOk()
            ->assertSeeText(__('book_group.grouped'));

        $book->refresh();
        $book2->refresh();

        $this->assertTrue($book->isInGroup());
        $this->assertTrue($book2->isInGroup());

        $this->assertFalse($book->isMainInGroup());
        $this->assertTrue($book2->isMainInGroup());

        $this->assertEquals($user->id, $book->connect_user_id);
        $this->assertNotNull($book->connected_at);
    }

    public function testAttachHttpError1()
    {
        $user = User::factory()->create()->fresh();
        $user->group->connect_books = true;
        $user->push();

        $book = Book::factory()->accepted()->create();

        $response = $this->actingAs($user)
            ->followingRedirects()
            ->post(route('books.group.attach', $book), ['edition_id' => $book->id], [
                'HTTP_REFERER' => route('books.editions.edit', ['book' => $book])
            ]);
        $response->assertOk()
            ->assertSeeText(__('book_group.book_to_be_attached_must_not_coincide_with_the_one_to_which_it_is_attached'));
    }

    public function testAttachHttpError2()
    {
        $user = User::factory()->create()->fresh();
        $user->group->connect_books = true;
        $user->push();

        $mainBook = Book::factory()->with_minor_book()->create();

        $minorBook = $mainBook->groupedBooks()->first();

        $response = $this->actingAs($user)
            ->followingRedirects()
            ->post(route('books.group.attach', $mainBook), ['edition_id' => $minorBook->id], [
                'HTTP_REFERER' => route('books.editions.edit', ['book' => $mainBook])
            ]);
        $response->assertOk()
            ->assertSeeText(__('book_group.book_is_already_attached_to_this_book'));
    }

    public function testAttachHttpError3()
    {
        $user = User::factory()->create()->fresh();
        $user->group->connect_books = true;
        $user->push();

        $book = Book::factory()->accepted()->create();

        $response = $this->actingAs($user)
            ->followingRedirects()
            ->get(route('books.group.detach', $book), ['edition_id' => $book->id]);
        $response->assertOk()
            ->assertSeeText(__('book_group.book_is_not_grouped'));
    }

    public function testAttachHttp()
    {
        config(['activitylog.enabled' => true]);

        $user = User::factory()->create()->fresh();
        $user->group->connect_books = true;
        $user->push();

        $book = Book::factory()->accepted()->create();
        $book2 = Book::factory()->accepted()->create();

        $response = $this->actingAs($user)
            ->post(route('books.group.attach', ['book' => $book->id]), ['edition_id' => $book2->id], [
                'HTTP_REFERER' => route('books.editions.edit', ['book' => $book])
            ]);
        $response->assertSessionHasNoErrors()
            ->assertRedirect();

        $book->refresh();
        $book2->refresh();

        $this->assertEquals(1, $book2->editions_count);

        $this->assertTrue($book->isMainInGroup());
        $this->assertTrue($book->isInGroup());

        $this->assertFalse($book2->isMainInGroup());
        $this->assertTrue($book2->isInGroup());

        $this->assertEquals(1, $book2->activities()->count());
        $activity = $book2->activities()->first();
        $this->assertEquals('group', $activity->description);
        $this->assertEquals($user->id, $activity->causer_id);
        $this->assertEquals('user', $activity->causer_type);
    }

    public function testAttachHttpIfBookInAnotherGroup()
    {
        $user = User::factory()->create()->fresh();
        $user->group->connect_books = true;
        $user->push();

        $mainBook = Book::factory()->with_minor_book()->create();

        $minorBook = $mainBook->groupedBooks()->first();

        $mainBook2 = Book::factory()->with_minor_book()->create();

        $minorBook2 = $mainBook2->groupedBooks()->first();

        $response = $this->actingAs($user)
            ->post(route('books.group.attach', ['book' => $mainBook->id]), ['edition_id' => $minorBook2->id], [
                'HTTP_REFERER' => route('books.editions.edit', ['book' => $mainBook])
            ]);
        $response->assertSessionHasNoErrors()
            ->assertRedirect();

        $mainBook->refresh();
        $mainBook2->refresh();
        $minorBook->refresh();
        $minorBook2->refresh();

        $this->assertEquals(2, $mainBook->editions_count);
        $this->assertEquals(0, $mainBook2->editions_count);

        $this->assertTrue($minorBook->isInGroup());
        $this->assertTrue($minorBook2->isInGroup());

        $this->assertTrue($mainBook->isInGroup());
        $this->assertFalse($mainBook2->isInGroup());
    }

    public function testDeleteOldestRepeatVote()
    {
        $user = User::factory()->create();

        $mainBook = Book::factory()->create();

        $minorBook = Book::factory()->create();

        $vote = BookVote::factory()->create([
            'book_id' => $mainBook->id,
            'create_user_id' => $user->id,
            'user_updated_at' => now()
        ]);

        $vote2 = BookVote::factory()->create([
            'book_id' => $minorBook->id,
            'create_user_id' => $user->id,
            'user_updated_at' => now()
        ]);

        BookGroupJob::dispatch($mainBook, $minorBook);

        $vote->refresh();
        $vote2->refresh();
        $user->refresh();

        $this->assertFalse($vote->trashed());
        $this->assertTrue($vote2->trashed());

        $this->assertEquals(1, $user->book_rate_count);
        $this->assertEquals(1, $mainBook->user_vote_count);
        $this->assertEquals(0, $minorBook->user_vote_count);
    }

    public function testDeleteOldestRepeatBookReadStatus()
    {
        $user = User::factory()->create();

        $mainBook = Book::factory()->create();

        $minorBook = Book::factory()->create();

        $status = BookStatus::factory()->create([
            'status' => 'readed',
            'book_id' => $mainBook->id,
            'user_id' => $user->id,
            'user_updated_at' => now()
        ]);

        $status2 = BookStatus::factory()->create([
            'status' => 'read_later',
            'book_id' => $minorBook->id,
            'user_id' => $user->id,
            'user_updated_at' => now()->subMonth()
        ]);

        BookGroupJob::dispatch($mainBook, $minorBook);

        $status->refresh();
        $user->refresh();

        $this->assertEquals(1, $user->book_read_statuses()->count());

        $this->assertEquals(1, $user->book_read_count);
        $this->assertEquals(0, $user->book_read_later_count);
    }

    public function testReadStatusesUpdated()
    {
        $mainBook = Book::factory()->create();
        $minorBook = Book::factory()->create();

        $status = BookStatus::factory()->create([
            'status' => 'readed',
            'book_id' => $mainBook->id
        ]);

        $status2 = BookStatus::factory()->create([
            'status' => 'read_now',
            'book_id' => $minorBook->id
        ]);

        BookGroupJob::dispatch($mainBook, $minorBook);

        $mainBook->refresh();
        $minorBook->refresh();

        $this->assertEquals(1, $mainBook->user_read_count);
        $this->assertEquals(1, $mainBook->user_read_now_count);

        $this->assertEquals(1, $minorBook->user_read_count);
        $this->assertEquals(1, $minorBook->user_read_now_count);

        $status->refresh();
        $status2->refresh();

        $this->assertEquals(1, $status->user->book_read_count);
        $this->assertEquals(0, $status->user->book_read_now_count);

        $this->assertEquals(0, $status2->user->book_read_count);
        $this->assertEquals(1, $status2->user->book_read_now_count);
    }

    public function testVotedUpdated()
    {
        $mainBook = Book::factory()->create();
        $minorBook = Book::factory()->create();

        $vote = BookVote::factory()->create(['book_id' => $mainBook->id]);

        $vote2 = BookVote::factory()->create(['book_id' => $minorBook->id]);

        BookGroupJob::dispatch($mainBook, $minorBook);

        $mainBook->refresh();
        $minorBook->refresh();

        $this->assertEquals(2, $mainBook->user_vote_count);
        $this->assertEquals(2, $minorBook->user_vote_count);

        $vote->refresh();
        $vote2->refresh();

        $this->assertEquals(1, $vote->create_user->book_rate_count);
        $this->assertEquals(1, $vote2->create_user->book_rate_count);
    }

    public function testUpdateCountersImmediatelyTrue()
    {
        $mainBook = Book::factory()->create();

        $minorBook = Book::factory()->create();

        $vote = BookVote::factory()->create(['book_id' => $mainBook->id, 'vote' => 7]);

        $vote2 = BookVote::factory()->create(['book_id' => $minorBook->id, 'vote' => 3]);

        $status = BookStatus::factory()->create(['book_id' => $mainBook->id, 'status' => 'read_later']);

        $status2 = BookStatus::factory()->create(['book_id' => $minorBook->id, 'status' => 'read_now']);

        BookGroupJob::dispatch($mainBook, $minorBook, true, true, false);

        $status->refresh();
        $status2->refresh();

        $this->assertTrue($status->user->refresh_counters);
        $this->assertTrue($status2->user->refresh_counters);
    }

    public function testUpdateCountersImmediatelyFalse()
    {
        $mainBook = Book::factory()->create();

        $minorBook = Book::factory()->create();

        $vote = BookVote::factory()->create(['book_id' => $mainBook->id, 'vote' => 7]);

        $vote2 = BookVote::factory()->create(['book_id' => $minorBook->id, 'vote' => 3]);

        $status = BookStatus::factory()->create(['book_id' => $mainBook->id, 'status' => 'read_later']);

        $status2 = BookStatus::factory()->create(['book_id' => $minorBook->id, 'status' => 'read_now']);

        BookGroupJob::dispatch($mainBook, $minorBook);

        $status->refresh();
        $status2->refresh();

        $this->assertNull($status->user->refresh_counters);
        $this->assertNull($status2->user->refresh_counters);
    }

    public function testAttachBookWithTwoComments()
    {
        $mainBook = Book::factory()->create();

        $minorBook = Book::factory()->create();

        $comment = Comment::factory()->book()->create(['commentable_id' => $mainBook->id]);

        $comment2 = Comment::factory()->book()->create(['commentable_id' => $minorBook->id]);

        BookGroupJob::dispatch($mainBook, $minorBook);

        $comment->refresh();
        $comment2->refresh();
        $mainBook->refresh();
        $minorBook->refresh();

        $this->assertEquals($mainBook->id, $comment->commentable_id);
        $this->assertEquals($mainBook->id, $comment2->commentable_id);

        $this->assertEquals($mainBook->id, $comment->origin_commentable_id);
        $this->assertEquals($minorBook->id, $comment2->origin_commentable_id);

        $this->assertEquals(2, $mainBook->comment_count);
        $this->assertEquals(2, $minorBook->comment_count);
    }

    public function testLeaveBookVoteOnlyFromMainEdition()
    {
        $user = User::factory()->create();

        $user_updated_at = now()->subDays();

        $vote = BookVote::factory()->create([
            'create_user_id' => $user->id,
            'user_updated_at' => now()->subDays(2)
        ]);

        $vote2 = BookVote::factory()->create([
            'create_user_id' => $user->id,
            'user_updated_at' => now()->subDays(1)
        ]);

        $vote3 = BookVote::factory()->create([
            'create_user_id' => $user->id,
            'user_updated_at' => now()->subDays(3)
        ]);

        $book = $vote->book;
        $book2 = $vote2->book;
        $book3 = $vote3->book;

        BookGroupJob::dispatch($book3, $book2);
        BookGroupJob::dispatch($book3, $book);

        $vote->refresh();
        $vote2->refresh();
        $vote3->refresh();

        $this->assertTrue($vote->trashed());
        $this->assertTrue($vote2->trashed());
        $this->assertFalse($vote3->trashed());
    }

    public function testSeeBelowWarning()
    {
        $mainBook = Book::factory()->create();

        $minorBook = Book::factory()->create();

        $comment = Comment::factory()->book()->create(['commentable_id' => $mainBook->id]);

        $comment2 = Comment::factory()->book()->create(['commentable_id' => $minorBook->id]);

        $response = $this->get(route('books.show', ['book' => $mainBook]))
            ->assertOk()
            ->assertDontSeeText(__('book.below_are_comments_from_other_versions_and_publications'));

        $response = $this->get(route('books.show', ['book' => $minorBook]))
            ->assertOk()
            ->assertDontSeeText(__('book.below_are_comments_from_other_versions_and_publications'));

        BookGroupJob::dispatch($mainBook, $minorBook);

        $mainBook->refresh();
        $minorBook->refresh();

        $response = $this->get(route('books.show', ['book' => $mainBook]))
            ->assertOk()
            ->assertSeeText(__('book.below_are_comments_from_other_versions_and_publications'))
            ->assertSeeTextInOrder([$comment->text, $comment2->text]);

        $response = $this->get(route('books.show', ['book' => $minorBook]))
            ->assertOk()
            ->assertSeeText(__('book.below_are_comments_from_other_versions_and_publications'))
            ->assertSeeTextInOrder([$comment2->text, $comment->text]);
    }
}