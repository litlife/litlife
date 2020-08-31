<?php

namespace Tests\Feature\Comment;

use App\Book;
use App\Comment;
use App\CommentVote;
use App\Jobs\Book\BookGroupJob;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Tests\TestCase;

class CommentGoToTest extends TestCase
{
	public function testGoToIfOnReview()
	{
		$comment = factory(Comment::class)->create();
		$comment->statusSentForReview();
		$comment->save();

		$this->followingRedirects()
			->get(route('comments.go', $comment))
			->assertOk();
	}

	public function testGoToReplyToTopPost()
	{
		$book = factory(Book::class)->create();
		$book->statusAccepted();
		$book->save();

		$top_comment = factory(Comment::class)
			->create([
				'commentable_id' => $book->id,
				'commentable_type' => 'book'
			]);

		$comment_votes = factory(CommentVote::class, 10)
			->create([
				'comment_id' => $top_comment->id,
				'vote' => '1'
			]);

		$top_comment->refresh();

		$this->assertEquals($top_comment->vote, 10);

		Carbon::setTestNow(now()->addMinute());

		$comment = factory(Comment::class, 20)
			->create([
				'commentable_id' => $book->id,
				'commentable_type' => 'book'
			]);

		$reply = factory(Comment::class)
			->create([
				'commentable_id' => $book->id,
				'commentable_type' => 'book',
				'parent' => $top_comment->id
			]);

		$top_comment->refresh();

		$this->assertEquals($top_comment->children_count, 1);

		$this->followingRedirects()
			->get(route('comments.go', ['comment' => $reply->id]))
			->assertSeeText($reply->text);
	}

	public function testGoToIfBooksGrouped()
	{
		$book = factory(Book::class)
			->create();

		$book2 = factory(Book::class)
			->create();

		$comment = factory(Comment::class)
			->states('book')
			->create([
				'commentable_id' => $book->id
			]);

		$comment2 = factory(Comment::class)
			->states('book')
			->create([
				'commentable_id' => $book2->id
			]);

		BookGroupJob::dispatch($book, $book2);

		$url1 = route('books.show', ['book' => $book, 'page' => 1, 'comment' => $comment->id]) . '&#comment_' . $comment->id;
		$url2 = route('books.show', ['book' => $book2, 'page' => 1, 'comment' => $comment2->id]) . '&#comment_' . $comment2->id;

		$this->get(route('comments.go', $comment))
			->assertRedirect($url1);

		$this->get(route('comments.go', $comment2))
			->assertRedirect($url2);
	}

	public function testGoToIfBooksGroupedAndHasReply()
	{
		$book = factory(Book::class)
			->create();

		$book2 = factory(Book::class)
			->create();

		$comment = factory(Comment::class)
			->states('book')
			->create(['commentable_id' => $book->id]);

		$reply = factory(Comment::class)
			->states('book')
			->create(['commentable_id' => $book->id]);
		$reply->parent = $comment;
		$reply->save();

		$comment2 = factory(Comment::class)
			->states('book')
			->create(['commentable_id' => $book2->id]);

		$reply2 = factory(Comment::class)
			->states('book')
			->create(['commentable_id' => $book2->id]);
		$reply2->parent = $comment2;
		$reply2->save();

		BookGroupJob::dispatch($book, $book2);

		$url1 = route('books.show', ['book' => $book, 'page' => 1, 'comment' => $reply->id]) . '&#comment_' . $reply->id;
		$url2 = route('books.show', ['book' => $book2, 'page' => 1, 'comment' => $reply2->id]) . '&#comment_' . $reply2->id;

		$this->get(route('comments.go', $reply))
			->assertRedirect($url1);

		$this->get(route('comments.go', $reply2))
			->assertRedirect($url2);

		$response = $this->get($url1)
			->assertOk();

		$this->assertEquals($comment->id, Arr::get($response->original->gatherData(), 'comment')->id);

		$response = $this->get($url2)
			->assertOk();

		$this->assertEquals($comment2->id, Arr::get($response->original->gatherData(), 'comment')->id);
	}
}
