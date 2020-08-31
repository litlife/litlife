<?php

namespace Tests\Feature\Book\Section;

use App\Jobs\Book\BookUpdatePageNumbersJob;
use App\Section;
use App\User;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;

class NoteTest extends TestCase
{
	/**
	 * A basic test example.
	 *
	 * @return void
	 */
	public function testStoreChildHttp()
	{
		$title = $this->faker->realText(100);
		$content = '<p>' . $this->faker->realText(100) . '</p>';

		$user = factory(User::class)->create();
		$user->group->edit_self_book = true;
		$user->group->edit_other_user_book = true;
		$user->push();

		$section = factory(Section::class)
			->states('note')
			->create();

		$book = $section->book;
		$book->statusAccepted();
		$book->save();

		$this->actingAs($user)
			->post(route('books.notes.store', ['book' => $book, 'parent' => $section->inner_id]),
				[
					'title' => $title,
					'content' => $content
				])
			->assertSessionHasNoErrors()
			->assertRedirect(route('books.notes.index', $book));

		$section->refresh();

		$last_section = $book->sections()
			->where('type', 'note')
			->orderBy('id', 'desc')
			->first();

		$this->assertEquals(0, $section->children->count());

		$this->assertEquals($title, $last_section->title);
		$this->assertEquals($content, $last_section->getContent());
		$this->assertEquals('note', $last_section->type);

		$this->assertTrue($section->isRoot());
		$this->assertTrue($last_section->isRoot());

		$this->assertFalse($last_section->isChildOf($section));
		$this->assertFalse($section->isChildOf($last_section));

		$this->assertEquals(2, $book->fresh()->notes_count);
	}

	public function testIndexIfBookPrivate()
	{
		$section = factory(Section::class)
			->create();

		$book = $section->book;
		$book->statusPrivate();
		$book->save();
		$book->refresh();

		$user = factory(User::class)
			->create();

		$this->get(route('books.notes.index', ['book' => $book]))
			->assertForbidden();

		$this->actingAs($user)
			->get(route('books.notes.index', ['book' => $book]))
			->assertForbidden();

		$book->create_user_id = $user->id;
		$book->save();
		$book->refresh();

		$this->actingAs($user)
			->get(route('books.notes.index', ['book' => $book]))
			->assertOk()
			->assertSeeText($section->name);
	}

	public function testMoveToSections()
	{
		Bus::fake(BookUpdatePageNumbersJob::class);

		$user = factory(User::class)->create();
		$user->group->edit_self_book = true;
		$user->group->edit_other_user_book = true;
		$user->push();

		$section = factory(Section::class)
			->states('note')
			->create();

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
		$user = factory(User::class)->create();
		$user->group->edit_self_book = true;
		$user->group->edit_other_user_book = true;
		$user->push();

		$section = factory(Section::class)
			->states('note')
			->create();

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
