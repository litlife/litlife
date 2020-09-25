<?php

namespace Tests\Feature\Artisan;

use App\Author;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class AuthorSetLPStatusForAllBooksTest extends TestCase
{
	public function testCommand()
	{
		$author = factory(Author::class)
			->states('with_book')
			->create();

		$book = $author->any_books()->first();
		$book->is_lp = false;
		$book->save();

		$this->assertFalse($book->is_lp);

		Artisan::call('author:set_lp_status_for_all_books', ['author_id' => $author->id]);

		$book->refresh();

		$this->assertTrue($book->is_lp);
	}
}
