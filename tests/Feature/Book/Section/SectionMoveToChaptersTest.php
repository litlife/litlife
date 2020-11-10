<?php

namespace Tests\Feature\Book\Section;

use App\Jobs\Book\BookUpdatePageNumbersJob;
use App\Section;
use App\User;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;

class SectionMoveToChaptersTest extends TestCase
{
	public function testMoveToSections()
	{
		Bus::fake(BookUpdatePageNumbersJob::class);

		$user = User::factory()->create();
		$user->group->edit_self_book = true;
		$user->group->edit_other_user_book = true;
		$user->push();

		$section = Section::factory()->note()->create();

		$book = $section->book;
		$book->statusAccepted();
		$book->user_edited_at = null;
		$book->edit_user_id = null;
		$book->save();
		$book->refresh();

		$this->assertEquals(0, $book->sections_count);
		$this->assertEquals(1, $book->notes_count);

		$this->actingAs($user)
			->post(route('books.notes.move_to_sections', ['book' => $book]),
				['notes_ids' => $section->id])
			->assertOk()
			->assertJson(['notes_ids' => [$section->id]]);

		$book->refresh();

		$this->assertEquals(1, $book->sections_count);
		$this->assertEquals(0, $book->notes_count);
		$this->assertNotNull($book->user_edited_at);
		$this->assertEquals($user->id, $book->edit_user_id);
		$this->assertTrue($book->isWaitedCreateNewBookFiles());

		Bus::assertDispatched(BookUpdatePageNumbersJob::class);
	}

	public function testMoveToChapters()
	{
		$user = User::factory()->create();
		$user->group->edit_self_book = true;
		$user->group->edit_other_user_book = true;
		$user->push();

		$section = Section::factory()->note()->create();

		$book = $section->book;
		$book->statusAccepted();
		$book->user_edited_at = null;
		$book->edit_user_id = null;
		$book->save();
		$book->refresh();

		$this->assertTrue($section->isNote());
		$this->assertEquals(0, $book->sections_count);
		$this->assertEquals(1, $book->notes_count);

		$this->actingAs($user)
			->post(route('books.sections.move_to_chapters', ['book' => $book]),
				['ids' => $section->id])
			->assertOk()
			->assertJson(['ids' => [$section->id]]);

		$book->refresh();
		$section->refresh();

		$this->assertTrue($section->isChapter());
		$this->assertEquals(1, $book->sections_count);
		$this->assertEquals(0, $book->notes_count);
		$this->assertNotNull($book->user_edited_at);
		$this->assertEquals($user->id, $book->edit_user_id);
		$this->assertTrue($book->isWaitedCreateNewBookFiles());
	}
}
