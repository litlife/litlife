<?php

namespace Tests\Feature\Sequence;

use App\Comment;
use App\Sequence;
use Tests\TestCase;

class SequenceShowTest extends TestCase
{
	public function testIsOk()
	{
		$sequence = Sequence::factory()->with_book()->create();

		$this->assertEquals(1, $sequence->book_count);

		$book = $sequence->books()->first();

		$this->get(route('sequences.show', $sequence))
			->assertOk()
			->assertSeeText(trans_choice('book.books', 2) . ' 1')
			->assertSeeText($book->title);
	}

	public function testComments()
	{
		$sequence = Sequence::factory()->with_book()->create();

		$book = $sequence->books()->first();

		$comment = Comment::factory()->create(['commentable_type' => 'book', 'commentable_id' => $book->id]);

		$this->get(route('sequences.comments', ['sequence' => $sequence->id]))
			->assertOk()
			->assertSeeText($comment->text)
			->assertSee('comments-search-container');
	}
}
