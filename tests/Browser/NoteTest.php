<?php

namespace Tests\Browser;

use App\Book;
use App\Enums\StatusEnum;
use App\Jobs\Book\UpdateBookNotesCount;
use App\Section;
use App\User;
use Tests\DuskTestCase;

class NoteTest extends DuskTestCase
{
	/**
	 * A Dusk test example.
	 *
	 * @return void
	 */

	public function testCreateAndEdit()
	{
		$this->browse(function ($browser) {

			$user = User::factory()->create();

			$book = Book::factory()->create([
				'create_user_id' => $user->id,
				'status' => StatusEnum::Private
			]);

			$title = $this->faker->realText(100);
			$text = $this->faker->realText(300);

			// create
			$browser->resize(1000, 1000)
				->loginAs($user)
				->visit(route('books.show', $book))
				->click('#bookDropdownMenuButton')
				->with('[aria-labelledby=bookDropdownMenuButton]', function ($dropdown_menu) {
					$dropdown_menu->assertSee(mb_strtolower(__('common.edit')))
						->clickLink(__('common.edit'));
				})
				->clickLink(__('model.notes'))
				->assertSee(__('note.nothing_found'))
				->clickLink(__('section.add_new_note'))
				->value('[name=title]', $title)
				->value('[name=content]', $text)
				->press(__('common.save'))
				->waitForText($title, 20)
				->assertSee($title)
				->clickLink($title)
				->assertSee($text);

			$book->fresh();

			$book = Book::any()->findOrFail($book->id);

			$this->assertEquals($book->notes_count, 1);

			$section = $book->sections->first();

			$new_title = $this->faker->realText(100);
			$new_text = $this->faker->realText(300);

			// edit
			$browser->visit(route('books.notes.index', $book))
				->with('.section[data-id="' . $section->id . '"][data-inner-id="' . $section->inner_id . '"]', function ($item) {
					$item->click('.btn-group')
						->with('.dropdown-menu', function ($dropdown_menu) {
							$dropdown_menu->assertSee(mb_strtolower(__('common.edit')))
								->clickLink(__('common.edit'));
						});
				})
				->assertValue('[name=title]', $title)
				->assertValue('[name=content]', $section->getContent())
				->value('[name=title]', $new_title)
				->value('[name=content]', $new_text)
				->press(__('common.save'))
				->waitForText(__('common.data_saved'))
				->assertSee(__('common.data_saved'));

			$book->forceDelete();
		});
	}

	public function testDeleteAndRestore()
	{
		$this->browse(function ($browser) {

			$section = Section::factory()->create(['type' => 'note']);

			$browser->resize(1000, 1000)
				->loginAs($section->book->create_user)
				->visit(route('books.notes.index', $section->book))
				->with('.section[data-id="' . $section->id . '"][data-inner-id="' . $section->inner_id . '"]', function ($item) {
					$item->click('.btn-group')
						->with('.dropdown-menu', function ($dropdown_menu) {
							$dropdown_menu->assertSee(mb_strtolower(__('common.delete')))
								->clickLink(__('common.delete'));
						})
						->waitFor('.transparency')
						->assertVisible('.transparency');
				});

			$section = $section->fresh();

			$this->assertTrue($section->trashed());

			$browser->with('.section[data-id="' . $section->id . '"][data-inner-id="' . $section->inner_id . '"]', function ($item) {
				$item->click('.btn-group')
					->with('.dropdown-menu', function ($dropdown_menu) {
						$dropdown_menu->assertSee(mb_strtolower(__('common.restore')))
							->clickLink(__('common.restore'));
					})
					->waitUntilMissing('.transparency');
			});

			$section = $section->fresh();

			$this->assertFalse($section->trashed());
		});
	}

	public function testMoveToChapters()
	{
		$this->browse(function ($browser) {

			$book = Book::factory()->with_create_user()->private()->create();

			$user = $book->create_user;

			$section = Section::factory()->create(['book_id' => $book->id, 'type' => 'note']);

			$section2 = Section::factory()->create(['book_id' => $book->id, 'type' => 'note']);

			UpdateBookNotesCount::dispatch($book);

			$book->refresh();

			$this->assertEquals(0, $book->sections_count);
			$this->assertEquals(2, $book->notes_count);

			$browser->resize(1000, 1000)
				->loginAs($user)
				->visit(route('books.notes.index', $book))
				->assertDontSee(__('note.move_to_section'))
				->with('.list-group-item[data-id="' . $section->id . '"]', function ($item) {
					$item->click('.dropdown-toggle')
						->whenAvailable('.dropdown-menu', function ($menu) {
							$menu->click('.move-to-chapters');
						});
				})
				->waitUntilMissing('.list-group-item[data-id="' . $section->id . '"]')
				->visit(route('books.sections.index', $book));

			$book->refresh();

			$this->assertEquals(1, $book->sections_count);
			$this->assertEquals(1, $book->notes_count);
		});
	}

	public function testChangePosition()
	{
		$this->browse(function ($browser) {

			$book = Book::factory()->with_create_user()->private()->create();

			$user = $book->create_user;

			$section = Section::factory()->create(['book_id' => $book->id, 'type' => 'note']);

			$section2 = Section::factory()->create(['book_id' => $book->id, 'type' => 'note']);

			$book->refresh();

			$sections = Section::scoped(['book_id' => $book->id, 'type' => 'note'])
				->defaultOrder()->get();

			$this->assertEquals($section->id, $sections[0]->id);
			$this->assertEquals($section2->id, $sections[1]->id);

			$browser->resize(1000, 1000)
				->loginAs($user)
				->visit(route('books.notes.index', $book))
				->with('.list-group-item[data-id="' . $section2->id . '"]', function ($item) {
					$item->click('.dropdown-toggle')
						->dragUp('.handle', 500);
				})
				->press(__('note.save_position'))
				->visit(route('books.notes.index', $book));

			$book->refresh();

			$sections = Section::scoped(['book_id' => $book->id, 'type' => 'note'])
				->defaultOrder()->get();

			$this->assertEquals($section->id, $sections[1]->id);
			$this->assertEquals($section2->id, $sections[0]->id);
			/*
						$this->assertEquals(0, $book->sections_count);
						$this->assertEquals(2, $book->notes_count);

			*/
		});
	}

	public function testTooltip()
	{
		$this->browse(function ($browser) {

			$user = User::factory()->admin()->create();

			$book = Book::factory()->create();

			$note_text = 'текст сноски';

			$note = factory(Section::class)
				->states('note')
				->create([
					'content' => $note_text,
					'book_id' => $book->id
				]);

			$section_text = 'текст главы <a href="#' . $note->getSectionId() . '">сноска</a>';

			$section = Section::factory()->create([
					'content' => $section_text,
					'book_id' => $book->id
				]);

			$book->refresh();

			$browser->resize(1000, 1000)
				->loginAs($user)
				->visit(route('books.sections.show', ['book' => $book, $section->inner_id]))
				->assertVisible('.book_text')
				->with('.book_text', function ($text) {
					$text->assertSee('текст главы')
						->assertSee('сноска')
						->click('[data-type="note"]');
				})
				->waitFor('.bootbox')
				->with('.bootbox', function ($modal) {
					$modal->waitForText('текст сноски');
				});
		});
	}
}
