<?php

namespace Tests\Feature\Book\Section;

use App\Book;
use App\Jobs\Book\BookUpdatePageNumbersJob;
use App\User;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;

class SectionDeleteTest extends TestCase
{
	public function testDeleteAndRestore()
	{
		Bus::fake(BookUpdatePageNumbersJob::class);

		$user = factory(User::class)->create();
		$user->group->edit_self_book = true;
		$user->group->edit_other_user_book = true;
		$user->push();

		$book = factory(Book::class)
			->states('with_section')
			->create()
			->fresh();

		$characters_count = $book->characters_count;

		$section = $book->sections()->first();

		$this->actingAs($user)
			->delete(route('books.sections.destroy', ['book' => $book, 'section' => $section->inner_id]))
			->assertRedirect(route('books.sections.show', ['book' => $book, 'section' => $section->inner_id]));

		$this->assertSoftDeleted($section->fresh());
		$this->assertEquals(0, $book->fresh()->characters_count);

		$this->actingAs($user)
			->delete(route('books.sections.destroy', ['book' => $book, 'section' => $section->inner_id]))
			->assertRedirect(route('books.sections.show', ['book' => $book, 'section' => $section->inner_id]));

		$this->assertFalse($section->fresh()->trashed());
		$this->assertEquals($characters_count, $book->fresh()->characters_count);

		Bus::assertDispatched(BookUpdatePageNumbersJob::class);
	}
}
