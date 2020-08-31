<?php

namespace Tests\Feature\Book\Section;

use App\Book;
use App\Jobs\Book\BookUpdatePageNumbersJob;
use App\User;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;

class SectionSavePositionTest extends TestCase
{
	public function testSavePosition()
	{
		Bus::fake(BookUpdatePageNumbersJob::class);

		$book = factory(Book::class)
			->states('with_three_sections')
			->create();

		$user = factory(User::class)
			->states('admin')
			->create();

		$chapters = $book->sections()->chapter()->defaultOrder()->get();

		$chapter1 = $chapters->get(0);
		$chapter2 = $chapters->get(1);
		$chapter3 = $chapters->get(2);

		$hierarchy = [
			[
				'id' => $chapter3->id,
				'name' => 'section_' . $chapter1->id,
			],
			[
				'id' => $chapter2->id,
				'name' => 'section_' . $chapter1->id,
			],
			[
				'id' => $chapter1->id,
				'name' => 'section_' . $chapter1->id,
			],
		];

		$this->actingAs($user)
			->post(route('books.sections.save_position', $book),
				['hierarchy' => $hierarchy]
			)
			->assertOk();

		$book->refresh();

		$this->assertEquals($user->id, $book->edit_user_id);

		$chapters = $book->sections()->chapter()->defaultOrder()->get();

		$this->assertEquals($chapter3->id, $chapters->get(0)->id);
		$this->assertEquals($chapter2->id, $chapters->get(1)->id);
		$this->assertEquals($chapter1->id, $chapters->get(2)->id);

		Bus::assertDispatched(BookUpdatePageNumbersJob::class);
	}
}