<?php

namespace Tests\Feature\Book;

use App\Award;
use App\Book;
use App\BookAward;
use App\User;
use Illuminate\Support\Str;
use Tests\TestCase;

class BookAwardTest extends TestCase
{
	/**
	 * A basic test example.
	 *
	 * @return void
	 */
	public function testStoreHttp()
	{
		$book = Book::factory()->create();

		$award = Award::factory()->create();

		$user = User::factory()->create();
		$user->group->awards = true;
		$user->push();

		$year = $this->faker->year;

		$response = $this->actingAs($user)
			->post(route('books.awards.store', ['book' => $book->id]),
				['award' => $award->id, 'year' => $year])
			->assertSessionHasNoErrors()
			->assertRedirect();

		$book->refresh();

		$this->assertEquals(1, $book->awards_count);
		$this->assertEquals($year, $book->awards()->first()->year);
		$this->assertEquals($award->id, $book->awards()->first()->award_id);
	}

	public function testDestroyHttp()
	{
		$bookAward = BookAward::factory()->create();

		$user = User::factory()->create();
		$user->group->awards = true;
		$user->push();

		$this->assertEquals(1, $bookAward->book()->first()->awards_count);

		$response = $this->actingAs($user)
			->delete(route('books.awards.destroy', ['book' => $bookAward->book()->first()->id, 'award' => $bookAward->award_id]))
			->assertSessionHasNoErrors()
			->assertOk();

		$this->assertNull($bookAward->book()->first()->awards()->first());
		$this->assertEquals(0, $bookAward->book()->first()->awards_count);
	}

	public function testAwardNotInteger()
	{
		$this->get(route('awards.show', ['award' => Str::random(8)]))
			->assertNotFound();
	}
}
