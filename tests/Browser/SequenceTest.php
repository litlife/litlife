<?php

namespace Tests\Browser;

use App\Book;
use App\Comment;
use App\Sequence;
use Illuminate\Support\Str;
use Tests\DuskTestCase;

class SequenceTest extends DuskTestCase
{
	/**
	 * A Dusk test example.
	 *
	 * @return void
	 */
	public function testTab()
	{
		$this->browse(function ($browser) {

			$sequence = Sequence::factory()->create();

			$book = Book::factory()->create(['title' => Str::random(8)]);

			$book->sequences()->sync([$sequence->id]);

			$comment = Comment::factory()->create();
			$comment->commentable_type = 'book';
			$comment->commentable_id = $book->id;
			$comment->save();

			$browser->resize(1000, 2000)
				->visit(route('sequences.show', $sequence))
				->assertSee($sequence->name)
				->assertSee($book->title)
				->assertSee(trans_choice('book.books', 2))
				->assertSee(__('sequence.book_comments'))
				->assertVisible('[href="#comments"]')
				->click('[href="#comments"]')
				->waitFor('.comments-search-container')
				->whenAvailable('#comments.active', function ($panel) use ($comment) {
					$panel->assertSee($comment->text);
				}, 15)
				->click('[href="#books"]')
				->whenAvailable('#books.active', function ($panel) use ($book) {
					$panel->assertSee($book->title);
				});
		});
	}

	public function testSequenceSearch()
	{
		$this->browse(function ($browser) {

			$title = Str::random(10);

			$sequence = Sequence::factory()->create(['name' => $title]);

			$browser->resize(1000, 2000)
				->visit(route('sequences'))
				->assertVisible('.sequences-search-container')
				->with('.sequences-search-container', function ($container) use ($title) {
					$container->with('.sequence-form', function ($form) use ($title) {
						$form->type('search', $title);
					});
				})
				->waitFor('.list.loading-cap')
				->waitUntilMissing('.list.loading-cap')
				->with('.list', function ($list) use ($title) {
					$list->assertSee($title);
				});
		});
	}
}
