<?php

namespace Tests\Browser;

use App\Book;
use App\BookVote;
use App\Comment;
use App\User;
use Tests\DuskTestCase;

class HomeTest extends DuskTestCase
{
	/**
	 * A Dusk test example.
	 *
	 * @return void
	 */

	public function testLatestComments()
	{
		$this->browse(function ($browser) {

			$book = Book::factory()->with_writer()->accepted()->create();

			$user = User::factory()->create();

			$book_vote = BookVote::factory()->create([
				'book_id' => $book->id,
				'create_user_id' => $user->id
			]);

			$comment = factory(Comment::class)
				->states('book')
				->create([
					'commentable_id' => $book->id,
					'create_user_id' => $user->id
				]);

			$browser->resize(1000, 1000)
				->visit(route('home.latest_comments'))
				->with('.item[data-id="' . $comment->id . '"]', function ($item) use ($comment, $book_vote, $book) {
					$item->assertSee($comment->text)
						->assertSee(__('common.vote') . ': ' . $book_vote->vote)
						->assertSee($book->title)
						->assertSee($book->writers()->first()->name);
				});
		});
	}

}
