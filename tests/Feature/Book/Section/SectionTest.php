<?php

namespace Tests\Feature\Book\Section;

use App\Attachment;
use App\Author;
use App\Book;
use App\Enums\StatusEnum;
use App\Jobs\Book\BookUpdatePageNumbersJob;
use App\Section;
use App\User;
use App\UserPurchase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SectionTest extends TestCase
{
	/**
	 * A basic test example.
	 *
	 * @return void
	 */
	public function testCreate()
	{
		Storage::fake(config('filesystems.default'));

		$content = '<p>' . $this->faker->text . ' <strong>' . $this->faker->sentence . '</strong></p>';

		$book = factory(Book::class)->create([
			'create_user_id' => 50000,
			'is_si' => false,
			'is_lp' => false,
			'age' => 0
		]);

		$title = $this->faker->realText(100);

		$section = new Section;
		$section->title = $title;
		$section->content = $content;
		$section->type = 'section';
		$book->sections()->save($section);

		$section->refresh();

		$this->assertEquals($title, $section->title);
		$this->assertEquals($content, $section->getContent());
	}

	public function testCreateHttp()
	{
		$user = factory(User::class)->create();
		$user->group->edit_self_book = true;
		$user->group->edit_other_user_book = true;
		$user->push();

		$book = factory(Book::class)
			->create();

		$this->actingAs($user)
			->get(route('books.sections.create', ['book' => $book]))
			->assertOk();
	}

	public function testStoreHttp()
	{
		Bus::fake(BookUpdatePageNumbersJob::class);

		$title = $this->faker->realText(100);
		$content = '<p>' . $this->faker->realText(100) . '</p>';

		$user = factory(User::class)->create();
		$user->group->edit_self_book = true;
		$user->group->edit_other_user_book = true;
		$user->push();

		$book = factory(Book::class)
			->create();

		$this->actingAs($user)
			->post(route('books.sections.store', ['book' => $book]),
				[
					'title' => $title,
					'content' => $content
				])
			->assertSessionHasNoErrors()
			->assertRedirect();

		$section = $book->sections()->first();

		$book->refresh();

		$this->assertEquals($title, $section->title);
		$this->assertEquals($content, $section->getContent());
		$this->assertEquals(1, $book->fresh()->sections_count);
		$this->assertEquals((new Section())->getCharacterCountInText($content), $section->character_count);
		$this->assertEquals((new Section())->getCharacterCountInText($content), $book->characters_count);

		Bus::assertDispatched(BookUpdatePageNumbersJob::class);
	}

	public function testStoreChildHttp()
	{
		$title = $this->faker->realText(100);
		$content = '<p>' . $this->faker->realText(100) . '</p>';

		$user = factory(User::class)->create();
		$user->group->edit_self_book = true;
		$user->group->edit_other_user_book = true;
		$user->push();

		$section = factory(Section::class)
			->create();

		$book = $section->book;
		$book->statusAccepted();
		$book->save();

		$this->actingAs($user)
			->post(route('books.sections.store', ['book' => $book, 'parent' => $section->inner_id]),
				[
					'title' => $title,
					'content' => $content
				])
			->assertSessionHasNoErrors()
			->assertRedirect();

		$section->refresh();
		$book->refresh();

		$child_section = $section->children->first();

		$this->assertEquals($title, $child_section->title);
		$this->assertEquals($content, $child_section->getContent());
		$this->assertEquals((new Section())->getCharacterCountInText($content), $child_section->character_count);
		$this->assertEquals($book->characters_count, $child_section->character_count + $section->character_count);

		$this->assertTrue($section->isRoot());
		$this->assertTrue($child_section->isChildOf($section));
		$this->assertEquals(1, $section->children->count());

		$this->assertEquals(2, $book->fresh()->sections_count);
	}

	public function testCreateChild()
	{
		$book = factory(Book::class)->create([
			'create_user_id' => 50000,
			'is_si' => false,
			'is_lp' => false,
			'age' => 0
		]);

		$parent_section = new Section;
		$parent_section->scoped(['book_id' => $book->id, 'type' => 'section']);
		$parent_section->title = $this->faker->realText(100);
		$parent_section->content = $this->faker->text;
		$parent_section->type = 'section';
		$book->sections()->save($parent_section);

		$parent_section->refresh();

		$section = new Section;
		$section->scoped(['book_id' => $book->id, 'type' => 'section']);
		$section->title = $this->faker->realText(100);
		$section->content = $this->faker->text;
		$section->type = 'section';
		$book->sections()->save($section);

		$section->appendToNode($parent_section)->save();

		$this->assertTrue($parent_section->isRoot());
		$this->assertTrue($section->isChildOf($parent_section));

		$this->assertCount(1, $parent_section->children);
	}

	public function testEditHttp()
	{
		$user = factory(User::class)->create();
		$user->group->edit_self_book = true;
		$user->group->edit_other_user_book = true;
		$user->push();

		$book = factory(Book::class)
			->states('with_section')
			->create();

		$section = $book->sections()->first();

		$this->actingAs($user)
			->get(route('books.sections.edit', ['book' => $book, 'section' => $section->inner_id]))
			->assertOk();
	}

	public function testUpdateHttp()
	{
		Bus::fake(BookUpdatePageNumbersJob::class);

		$user = factory(User::class)->create();
		$user->group->edit_self_book = true;
		$user->group->edit_other_user_book = true;
		$user->push();

		$book = factory(Book::class)
			->states('with_section')
			->create();

		$section = $book->sections()->first();

		$title = $this->faker->realText(100);
		$content = '<p>' . $this->faker->realText(100) . '</p>';

		$this->actingAs($user)
			->patch(route('books.sections.update', ['book' => $book, 'section' => $section->inner_id]),
				[
					'title' => $title,
					'content' => $content
				])
			->assertRedirect(route('books.sections.edit', ['book' => $book, 'section' => $section->inner_id]))
			->assertSessionHas(['success' => __('common.data_saved')]);

		$section->refresh();
		$book->refresh();

		$this->assertEquals($title, $section->title);
		$this->assertEquals($content, $section->getContent());
		$this->assertEquals((new Section())->getCharacterCountInText($content), $section->character_count);
		$this->assertEquals((new Section())->getCharacterCountInText($content), $book->characters_count);

		Bus::assertDispatched(BookUpdatePageNumbersJob::class);
	}

	public function testFulltextSearch()
	{
		$author = Section::FulltextSearch('Время&—&детство!')->limit(5)->get();

		$this->assertTrue(true);
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

		$this->get(route('books.sections.index', ['book' => $book]))
			->assertForbidden();

		$this->actingAs($user)
			->get(route('books.sections.index', ['book' => $book]))
			->assertForbidden();

		$book->create_user_id = $user->id;
		$book->save();
		$book->refresh();

		$this->actingAs($user)
			->get(route('books.sections.index', ['book' => $book]))
			->assertOk()
			->assertSeeText($section->name);
	}

	public function testIndexIfBookAccept()
	{
		$section = factory(Section::class)
			->create();

		$book = $section->book;
		$book->statusAccepted();
		$book->save();
		$book->refresh();

		$user = factory(User::class)
			->create();

		$this->get(route('books.sections.index', ['book' => $book]))
			->assertOk()
			->assertSeeText($section->name);

		$this->actingAs($user)
			->get(route('books.sections.index', ['book' => $book]))
			->assertOk()
			->assertSeeText($section->name);
	}

	public function testSplitOnPages()
	{
		config(['litlife.max_symbols_on_one_page' => 800]);

		$page1_text = '';
		for ($a = 0; $a < 8; $a++) {
			$page1_text .= '<p>' . $this->getTextEqualsLength(100) . '</p>';
		}

		$page2_text = '';
		for ($a = 0; $a < 8; $a++) {
			$page2_text .= '<p>' . $this->getTextEqualsLength(100) . '</p>';
		}

		$page3_text = '';
		for ($a = 0; $a < 4; $a++) {
			$page3_text .= '<p>' . $this->getTextEqualsLength(100) . '</p>';
		}

		$section_content = $page1_text . $page2_text . $page3_text;

		$section = factory(Section::class)->create();
		$section->content = $section_content;
		$section->save();

		$this->assertEquals(3, $section->pages()->count());

		$book = $section->book;

		$section->refresh();

		$this->assertEquals('u-section-1', $section->getSectionId());


		$this->assertEquals($section_content, $section->getContent());

		$this->assertEquals($page1_text, $section->pages[0]->content);
		$this->assertEquals(1, $section->pages[0]->page);

		$this->assertEquals($page2_text, $section->pages[1]->content);
		$this->assertEquals(2, $section->pages[1]->page);

		$this->assertEquals($page3_text, $section->pages[2]->content);
		$this->assertEquals(3, $section->pages[2]->page);
	}

	private function getTextEqualsLength($number)
	{
		$text = $this->faker->sentence($number);

		$text = preg_replace("/[[:space:]]+/iu", "", $text);

		return mb_substr($text, 0, $number);
	}


	public function testMoveToNote()
	{
		Bus::fake(BookUpdatePageNumbersJob::class);

		$user = factory(User::class)->create();
		$user->group->edit_self_book = true;
		$user->group->edit_other_user_book = true;
		$user->push();

		$section = factory(Section::class)
			->create();

		$book = $section->book;
		$book->statusAccepted();
		$book->user_edited_at = null;
		$book->edit_user_id = null;
		$book->save();
		$book->refresh();

		$this->assertTrue($section->isChapter());
		$this->assertEquals(1, $book->sections_count);
		$this->assertEquals(0, $book->notes_count);

		$this->actingAs($user)
			->post(route('books.sections.move_to_notes', ['book' => $book]),
				['ids' => $section->id])
			->assertOk()
			->assertJson(['ids' => [$section->id]]);

		$book->refresh();
		$section->refresh();

		$this->assertTrue($section->isNote());
		$this->assertEquals(0, $book->sections_count);
		$this->assertEquals(1, $book->notes_count);
		$this->assertNotNull($book->user_edited_at);
		$this->assertEquals($user->id, $book->edit_user_id);
		$this->assertFalse($book->isWaitedCreateNewBookFiles());

		Bus::assertDispatched(BookUpdatePageNumbersJob::class);
	}

	public function testCreateIfAuthorCanSaleHttp()
	{
		$author = factory(Author::class)
			->states('with_author_manager', 'with_book_for_sale')
			->create();

		$user = $author->managers->first()->user;
		$book = $author->books->first();
		$book->create_user()->associate($user);
		$book->push();

		$title = $this->faker->realText(100);
		$content = '<p>' . $this->faker->realText(100) . '</p>';

		$this->assertTrue($user->can('update', $book));

		$this->actingAs($user)
			->post(route('books.sections.store', ['book' => $book]),
				[
					'title' => $title,
					'content' => $content
				])
			->assertSessionHasNoErrors()
			->assertRedirect();

		$section = $book->sections()->orderBy('id', 'desc')->first();

		$this->assertEquals($title, $section->title);
		$this->assertEquals($content, $section->getContent());
		$this->assertTrue($section->isAccepted());
		/*
		$this->assertEquals($paid, $section->paid);
		$this->assertEquals($free_pages, $section->free_pages);
		*/
	}

	public function testUpdateIfAuthorCanSaleHttp()
	{
		$author = factory(Author::class)
			->states('with_author_manager', 'with_book_for_sale')
			->create();

		$user = $author->managers->first()->user;
		$book = $author->books->first();
		$book->create_user()->associate($user);
		$book->push();

		$title = $this->faker->realText(100);
		$content = '<p>' . $this->faker->realText(100) . '</p>';

		$section = $book->sections()->orderBy('id', 'desc')->first();

		$this->assertTrue($user->can('update', $section));

		$this->actingAs($user)
			->patch(route('books.sections.update', ['book' => $book, 'section' => $section->inner_id]),
				[
					'title' => $title,
					'content' => $content
				])
			->assertSessionHasNoErrors()
			->assertRedirect();

		$section->refresh();

		$this->assertEquals($title, $section->title);
		$this->assertEquals($content, $section->getContent());
		/*
		$this->assertEquals($paid, $section->paid);
		$this->assertEquals($free_pages, $section->free_pages);
		*/
	}

	public function testViewPolicyIfAllBookPaid()
	{
		$book = factory(Book::class)
			->create(['price' => 100, 'free_sections_count' => 0]);

		$section = factory(Section::class)
			->create(['book_id' => $book->id]);

		$section2 = factory(Section::class)
			->create(['book_id' => $book->id]);

		$reader = factory(User::class)
			->create();

		$this->assertFalse($reader->can('view', $section));
		$this->assertFalse($reader->can('view', $section2));
	}

	public function testViewPolicyIfTwoFirstSectionsFree()
	{
		$book = factory(Book::class)
			->create(['price' => 100, 'free_sections_count' => 2]);

		$free_section = factory(Section::class)
			->create(['book_id' => $book->id]);

		$free_section2 = factory(Section::class)
			->create(['book_id' => $book->id]);
		$free_section2->insertAfterNode($free_section);

		$section3 = factory(Section::class)
			->create(['book_id' => $book->id]);
		$section3->insertAfterNode($free_section2);

		$section4 = factory(Section::class)
			->create(['book_id' => $book->id]);
		$section4->insertAfterNode($section3);

		$reader = factory(User::class)
			->create();

		$this->assertTrue($reader->can('view', $free_section));
		$this->assertTrue($reader->can('view', $free_section2));
		$this->assertFalse($reader->can('view', $section3));
		$this->assertFalse($reader->can('view', $section4));
	}

	public function testViewPolicyIfTwoFirstSectionsFreeAndDescendants()
	{
		$book = factory(Book::class)
			->create(['price' => 100, 'free_sections_count' => 2]);

		$free_section = factory(Section::class)
			->create(['book_id' => $book->id]);

		$free_section2 = factory(Section::class)
			->create(['book_id' => $book->id]);
		$free_section->appendNode($free_section2);

		$section3 = factory(Section::class)
			->create(['book_id' => $book->id]);
		$free_section2->appendNode($section3);

		$section4 = factory(Section::class)
			->create(['book_id' => $book->id]);
		$section3->appendNode($section4);

		$reader = factory(User::class)
			->create();

		$this->assertTrue($reader->can('view', $free_section));
		$this->assertTrue($reader->can('view', $free_section2));
		$this->assertFalse($reader->can('view', $section3));
		$this->assertFalse($reader->can('view', $section4));
	}

	public function testPageViewPolicyIfFirstPageFreeAndUserBuyABook()
	{
		$book = factory(Book::class)
			->create(['price' => 100, 'free_sections_count' => 0]);

		$section = factory(Section::class)
			->create(['book_id' => $book->id]);

		$section2 = factory(Section::class)
			->create(['book_id' => $book->id]);

		$reader = factory(User::class)
			->create();

		$purchase = factory(UserPurchase::class)
			->create([
				'buyer_user_id' => $reader->id,
				'purchasable_type' => 'book',
				'purchasable_id' => $book->id,
			]);

		$this->assertTrue($reader->can('view', $section));
		$this->assertTrue($reader->can('view', $section2));
	}

	public function testPageViewPolicyIfSectionPaidAndUserAAuthor()
	{
		$author = factory(Author::class)
			->states('with_author_manager', 'with_book_for_sale')
			->create();

		$writer = $author->managers->first()->user;
		$book = $author->books->first();

		$section = factory(Section::class)
			->create(['book_id' => $book->id]);

		$this->assertTrue($book->isForSale());

		$this->assertTrue($writer->can('view', $section));
	}

	public function testPageViewPolicyIfSectionPaidAndUserGuest()
	{
		$book = factory(Book::class)
			->create(['price' => 100, 'free_sections_count' => 1]);

		$free_section = factory(Section::class)
			->create(['book_id' => $book->id]);

		$section2 = factory(Section::class)
			->create(['book_id' => $book->id]);
		$free_section->appendNode($section2);

		$this->assertTrue((new User)->can('view', $free_section));
		$this->assertFalse((new User)->can('view', $section2));
	}

	public function testViewPolicyIfBookReadAccessClosed()
	{
		$book = factory(Book::class)
			->create(['price' => 100, 'free_sections_count' => 1]);
		$book->readAccessDisable();
		$book->save();

		$section = factory(Section::class)
			->create(['book_id' => $book->id]);

		$user = factory(User::class)
			->create();

		$this->assertFalse($user->can('view', $section));
	}

	public function testViewPolicyIfBookPurchasedAndReadAccessClosed()
	{
		$book = factory(Book::class)
			->states('with_section')
			->create(['price' => 100, 'free_sections_count' => 1]);
		$book->readAccessDisable();
		$book->push();
		$book->refresh();

		$section = $book->sections()->first();

		$reader = factory(User::class)
			->create();

		$purchase = factory(UserPurchase::class)
			->create([
				'buyer_user_id' => $reader->id,
				'purchasable_type' => 'book',
				'purchasable_id' => $section->book->id,
			]);

		$this->assertFalse($reader->can('view', $section));
	}

	public function testViewSectionIfBookPurchasedHttp()
	{
		$book = factory(Book::class)
			->states('with_section')
			->create(['price' => 100, 'free_sections_count' => 1]);

		$book->delete();

		$section = $book->sections()->first();

		$reader = factory(User::class)
			->create();

		$purchase = factory(UserPurchase::class)
			->create([
				'buyer_user_id' => $reader->id,
				'purchasable_type' => 'book',
				'purchasable_id' => $section->book->id,
			]);

		$this->assertTrue($reader->can('view', $section));

		$this->actingAs($reader)
			->get(route('books.sections.show', ['book' => $book, 'section' => $section->inner_id]))
			->assertOk();
	}

	public function testViewSectionIfBookNotPurchasedAndBookDeletedHttp()
	{
		$book = factory(Book::class)
			->states('with_section')
			->create(['price' => 100, 'free_sections_count' => 1]);

		$book->delete();

		$section = $book->sections()->first();

		$reader = factory(User::class)
			->create();

		$this->assertFalse($reader->can('view', $section));

		$this->actingAs($reader)
			->get(route('books.sections.show', ['book' => $book, 'section' => $section->inner_id]))
			->assertForbidden()
			->assertSeeText(__('book.book_deleted'));
	}

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

	public function testIsChangedMethod()
	{
		$section = factory(Section::class)
			->create();

		$this->assertTrue($section->isChanged('character_count'));

		$section = Section::findOrFail($section->id);

		$character_count = $section->character_count;
		$section->character_count = $character_count;
		$section->save();

		$this->assertFalse($section->isChanged('character_count'));

		$section = Section::findOrFail($section->id);

		$this->assertFalse($section->isChanged('character_count'));


		$book = factory(Book::class)
			->create();

		$section = new Section();
		$section->fill([
			'title' => $this->faker->realText(100),
			'content' => $this->faker->realText(100),
		]);

		$this->assertTrue($section->isChanged('character_count'));

		$book->sections()->save($section);
	}

	public function testViewSectionIfItIsAPrivate()
	{
		$author = factory(Author::class)
			->states('with_author_manager', 'with_book')
			->create();

		$user = $author->managers->first()->user;
		$book = $author->books->first();
		$book->create_user()->associate($user);
		$book->push();

		$section = $book->sections()->first();
		$section->statusPrivate();
		$section->push();

		$this->actingAs($user)
			->get(route('books.sections.show', ['book' => $book, 'section' => $section->inner_id]))
			->assertOk();

		$user = factory(User::class)
			->create();

		$this->actingAs($user)
			->get(route('books.sections.show', ['book' => $book, 'section' => $section->inner_id]))
			->assertForbidden()
			->assertSeeText(__('section.access_is_limited'));

		$this->get(route('books.sections.show', ['book' => $book, 'section' => $section->inner_id]))
			->assertForbidden()
			->assertSeeText(__('section.access_is_limited'));
	}

	public function testViewSectionIfBookPurchasedAndFreeSectionsHttp()
	{
		$author = factory(Author::class)
			->states('with_book_for_sale', 'with_author_manager_can_sell')
			->create();

		$book = $author->any_books()->first();
		$book->free_sections_count = 1;
		$book->save();

		$section = $book->sections()->defaultOrder()->first();

		$user = factory(User::class)
			->create();

		$section2 = factory(Section::class)
			->create(['book_id' => $book->id, 'inner_id' => 3]);

		$this->get(route('books.sections.show', ['book' => $book, 'section' => $section->inner_id]))
			->assertOk();

		$this->followingRedirects()
			->get(route('books.sections.show', ['book' => $book, 'section' => $section2->inner_id]))
			->assertStatus(401);

		$this->actingAs($user)
			->get(route('books.sections.show', ['book' => $book, 'section' => $section2->inner_id]))
			->assertRedirect(route('books.purchase', $book))
			->assertSessionHas(['info' => __('book.paid_part_of_book')]);

		$this->actingAs($user)
			->followingRedirects()
			->get(route('books.sections.show', ['book' => $book, 'section' => $section2->inner_id]))
			->assertOk()
			->assertSeeText(__('book.paid_part_of_book'));
	}

	public function testViewIfBookPrivate()
	{
		$book = factory(Book::class)
			->states('private', 'with_section', 'with_create_user')
			->create();

		$section = $book->sections()->first();
		$user = $book->create_user;

		$this->assertTrue($book->isPrivate());
		$this->assertTrue($user->can('view', $section));

		$this->actingAs($user)
			->get(route('books.sections.show', ['book' => $book, 'section' => $section->inner_id]))
			->assertOk();
	}

	public function testViewIfSectionPrivateAndBookPrivate()
	{
		$section = factory(Section::class)
			->states('private', 'book_private')
			->create();

		$book = $section->book;
		$user = $section->book->create_user;

		$this->assertTrue($book->isPrivate());
		$this->assertTrue($section->isPrivate());
		$this->assertTrue($user->can('view', $section));

		$this->actingAs($user)
			->get(route('books.sections.show', ['book' => $book, 'section' => $section->inner_id]))
			->assertOk();
	}

	public function testUseDraftIfNotSectionTypePolicy()
	{
		$book = factory(Book::class)
			->states('with_author_manager', 'with_section')
			->create();

		$section = $book->sections()->first();
		$section->type = 'note';
		$section->push();

		$author = $book->authors->first();
		$user = $author->managers()->first()->user;

		$this->assertFalse($user->can('use_draft', $section));
	}

	public function testUseDraftIfManagerNotAuthorPolicy()
	{
		$book = factory(Book::class)
			->states('with_author_manager', 'with_section')
			->create();

		$section = $book->sections()->first();

		$author = $book->authors->first();
		$manager = $author->managers()->first();
		$manager->character = 'editor';
		$manager->push();

		$user = $manager->user;

		$this->assertFalse($user->can('use_draft', $section));
	}

	public function testUseDraftIfManagerAuthorPolicy()
	{
		$book = factory(Book::class)
			->states('with_author_manager', 'with_section')
			->create();

		$section = $book->sections()->first();
		$author = $book->authors->first();
		$manager = $author->managers()->first();

		$user = $manager->user;

		$this->assertTrue($user->can('use_draft', $section));
	}

	public function testShowRouteIsOkIfNoPagesIfPageFirst()
	{
		$section = factory(Section::class)
			->states('no_pages')
			->create();

		$section->book->statusAccepted();
		$section->push();

		$this->assertEquals(0, $section->pages()->count());
		$this->assertTrue($section->book->isAccepted());

		$this->get(route('books.sections.show', ['book' => $section->book, 'section' => $section->inner_id]))
			->assertOk();

		$this->get(route('books.sections.show', ['book' => $section->book, 'section' => $section->inner_id, 'page' => 1]))
			->assertOk();

		$this->get(route('books.sections.show', ['book' => $section->book, 'section' => $section->inner_id, 'page' => 2]))
			->assertNotFound();
	}

	public function testShowNotFound()
	{
		$section = factory(Section::class)
			->create();

		$book = $section->book;

		$this->get(route('books.sections.show', ['book' => $book, 'section' => $section->inner_id + 1]))
			->assertNotFound();
	}

	public function testXHTMLUsed()
	{
		$section = factory(Section::class)
			->create();

		$book = $section->book;

		$attachment = factory(Attachment::class)->create(['book_id' => $book->id]);

		$xhtml = '<p>текст <img src="' . $attachment->url . '" alt="test.jpg"/> текст</p>';

		$section->content = $xhtml;
		$section->save();
		$section->refresh();

		$this->assertEquals($xhtml, $section->getContent());
		$this->assertEquals(10, $section->character_count);
	}

	public function testGetFirstTag()
	{
		$section = factory(Section::class)->create();

		$section->content = '<div><div><div><div><p>текст</p><p>текст2</p></div></div></div></div>';
		$section->save();
		$section->refresh();

		$this->assertEquals('<p>текст</p><p>текст2</p>', $section->getContent());

		$section->content = '<div><div><div><div><p>текст</p></div></div></div></div>';
		$section->save();
		$section->refresh();

		$this->assertEquals('<p>текст</p>', $section->getContent());

		$section->content = '<div><div><div><div>текст</div></div></div></div>';
		$section->save();
		$section->refresh();

		$this->assertEquals('<div>текст</div>', $section->getContent());

		$section->content = '<div><div><div><div>текст</div><div>текст2</div></div></div></div>';
		$section->save();
		$section->refresh();

		$this->assertEquals('<div>текст</div><div>текст2</div>', $section->getContent());
	}

	public function testIsChildTagOnlyOne()
	{
		$section = new Section();

		$xhtml = '<body><div><div><div><div><p>текст</p><p>текст2</p></div></div></div></div></body>';
		$section->dom()->loadHTML('<meta http-equiv="Content-Type" content="text/html; charset=utf-8">' . $xhtml);
		$section->xpath(true);
		$this->assertTrue($section->isChildTagOnlyOne($section->dom()->getElementsByTagName('div')->item(0)));

		$xhtml = '<body><div><p>текст</p><p>текст2</p></div></body>';
		$section->dom()->loadHTML('<meta http-equiv="Content-Type" content="text/html; charset=utf-8">' . $xhtml);
		$section->xpath(true);
		$this->assertFalse($section->isChildTagOnlyOne($section->dom()->getElementsByTagName('div')->item(0)));

		$xhtml = '<body><p>текст</p><p>текст2</p></body>';
		$section->dom()->loadHTML('<meta http-equiv="Content-Type" content="text/html; charset=utf-8">' . $xhtml);
		$section->xpath(true);
		$this->assertFalse($section->isChildTagOnlyOne($section->dom()->getElementsByTagName('body')->item(0)));

		$xhtml = '<body><p>текст</p></body>';
		$section->dom()->loadHTML('<meta http-equiv="Content-Type" content="text/html; charset=utf-8">' . $xhtml);
		$section->xpath(true);
		$this->assertFalse($section->isChildTagOnlyOne($section->dom()->getElementsByTagName('body')->item(0)));

		$xhtml = '<body><div>текст</div></body>';
		$section->dom()->loadHTML('<meta http-equiv="Content-Type" content="text/html; charset=utf-8">' . $xhtml);
		$section->xpath(true);
		$this->assertFalse($section->isChildTagOnlyOne($section->dom()->getElementsByTagName('body')->item(0)));

		$xhtml = '<body><div>текст</div><div>текст</div></body>';
		$section->dom()->loadHTML('<meta http-equiv="Content-Type" content="text/html; charset=utf-8">' . $xhtml);
		$section->xpath(true);
		$this->assertFalse($section->isChildTagOnlyOne($section->dom()->getElementsByTagName('body')->item(0)));

		$xhtml = '<body><div><div><p>текст</p></div><div><p>текст</p></div></div></body>';
		$section->dom()->loadHTML('<meta http-equiv="Content-Type" content="text/html; charset=utf-8">' . $xhtml);
		$section->xpath(true);
		$this->assertTrue($section->isChildTagOnlyOne($section->dom()->getElementsByTagName('div')->item(0)));
		$this->assertFalse($section->isChildTagOnlyOne($section->dom()->getElementsByTagName('div')->item(1)));
		$this->assertFalse($section->isChildTagOnlyOne($section->dom()->getElementsByTagName('div')->item(2)));

		$xhtml = '<body>test</body>';
		$section->dom()->loadHTML('<meta http-equiv="Content-Type" content="text/html; charset=utf-8">' . $xhtml);
		$section->xpath(true);
		$this->assertFalse($section->isChildTagOnlyOne($section->dom()->getElementsByTagName('body')->item(0)));
	}

	public function testInnerId()
	{
		$book = factory(Book::class)->create();

		$section = factory(Section::class)
			->create(['book_id' => $book->id]);

		$section2 = factory(Section::class)
			->create(['book_id' => $book->id]);

		$section->refresh();
		$section2->refresh();

		$this->assertEquals(1, $section->inner_id);
		$this->assertEquals(2, $section2->inner_id);
	}

	public function testEmptyContent()
	{
		$section = factory(Section::class)->create();
		$section->content = '';
		$section->save();
		$section->refresh();

		$this->assertEquals(0, $section->pages()->count());
		$this->assertEquals('', $section->getContent());
	}

	public function testContentHandeledNotesAnchors()
	{
		$book = factory(Book::class)->create();

		$section = factory(Section::class)
			->create([
				'book_id' => $book->id,
				'content' => '<p><a href="#test2">текст</a> <span id="test1">текст</span></p>'
			])->fresh();

		$section2 = factory(Section::class)
			->states('note')
			->create([
				'book_id' => $book->id,
				'content' => '<p><a href="#test1">текст</a> <span id="test2">текст</span></p>'
			])->fresh();

		$this->assertEquals('<p><a data-type="note" data-section-id="2" href="http://dev.litlife.club/books/' . $book->id . '/notes/2?page=1#u-test2">текст</a> <span id="u-test1">текст</span></p>',
			$section->getContentHandeled());

		$this->assertEquals('<p><a data-type="section" data-section-id="1" href="http://dev.litlife.club/books/' . $book->id . '/sections/1?page=1#u-test1">текст</a> <span id="u-test2">текст</span></p>',
			$section2->getContentHandeled());
	}

	public function testContentHandeledImages()
	{
		$book = factory(Book::class)->create();

		$attachment = factory(Attachment::class)
			->create([
				'book_id' => $book->id
			])->fresh();

		$section = factory(Section::class)
			->create([
				'book_id' => $book->id,
				'content' => '<p><img src="' . $attachment->url . '"/></p>'
			])->fresh();

		$this->assertEquals('<p><img class="img-fluid"  src="' . $attachment->url . '" alt="test.jpeg"/></p>',
			$section->getContentHandeled());
	}

	public function testContentHandeledNotesAnchorsIfAnchorNotExists()
	{
		$book = factory(Book::class)->create();

		$section = factory(Section::class)
			->create([
				'book_id' => $book->id,
				'content' => '<p><a href="#test2">текст</a> <span id="test1">текст</span></p>'
			])->fresh();

		$this->assertEquals('<p><a href="#u-test2">текст</a> <span id="u-test1">текст</span></p>',
			$section->getContentHandeled());
	}

	public function testContentHandeledIfRemoteLinkWithHash()
	{
		$book = factory(Book::class)->create();

		$section = factory(Section::class)
			->create([
				'book_id' => $book->id,
				'content' => '<p><a href="https://example.com/test/?query=value#test2">текст</a> <span id="test1">текст</span></p>'
			])->fresh();

		$this->assertEquals('<p><a href="/away?url=https%3A%2F%2Fexample.com%2Ftest%2F%3Fquery%3Dvalue%23test2">текст</a> <span id="u-test1">текст</span></p>',
			$section->getContentHandeled());
	}

	public function testValidationMaxCharactersCountDontShow()
	{
		config(['litlife.max_section_characters_count' => 5]);

		$user = factory(User::class)
			->states('admin')
			->create();

		$section = factory(Section::class)
			->create();

		$book = $section->book;

		$title = $this->faker->realText(50);
		$content = '<p>1</p><p>2</p><p>3</p><p>4</p>';

		$this->actingAs($user)
			->patch(route('books.sections.update', ['book' => $book, 'section' => $section->inner_id]),
				[
					'title' => $title,
					'content' => $content
				])
			->assertSessionHasNoErrors()
			->assertRedirect();
	}

	public function testValidationMaxCharactersCountShow()
	{
		config(['litlife.max_section_characters_count' => 3]);

		$user = factory(User::class)
			->states('admin')
			->create();

		$section = factory(Section::class)
			->create();

		$book = $section->book;

		$title = $this->faker->realText(50);
		$content = '<p>1</p><p>2</p><p>3</p><p>4</p>';

		$this->actingAs($user)
			->patch(route('books.sections.update', ['book' => $book, 'section' => $section->inner_id]),
				[
					'title' => $title,
					'content' => $content
				])
			->assertSessionHasErrors([
				'content' => __('validation.max.string',
					[
						'max' => config('litlife.max_section_characters_count'),
						'attribute' => __('section.content')
					])])
			->assertRedirect();
	}

	public function testSeeOldContentIfMaxCharactersOverflowOnEdit()
	{
		config(['litlife.max_section_characters_count' => 3]);

		$user = factory(User::class)
			->states('admin')
			->create();

		$section = factory(Section::class)
			->create(['content' => 'старый контент']);

		$book = $section->book;

		$title = $this->faker->realText(50);
		$content = '<p>новый контент</p>';

		$this->actingAs($user)
			->get(route('books.sections.edit', ['book' => $book, 'section' => $section->inner_id]))
			->assertOk()
			->assertSeeText('старый контент');

		$this->actingAs($user)
			->followingRedirects()
			->patch(route('books.sections.update', ['book' => $book, 'section' => $section->inner_id]), [
				'title' => $title,
				'content' => $content
			])
			->assertOk()
			->assertDontSeeText('старый контент')
			->assertSeeText('новый контент');
	}

	public function testSeeOldContentIfMaxCharactersOverflowOnCreate()
	{
		config(['litlife.max_section_characters_count' => 3]);

		$user = factory(User::class)
			->states('admin')
			->create();

		$book = factory(Book::class)
			->create();

		$title = $this->faker->realText(50);
		$content = '<p>новый контент</p>';

		$this->actingAs($user)
			->get(route('books.sections.create', ['book' => $book]))
			->assertOk();

		$this->actingAs($user)
			->followingRedirects()
			->post(route('books.sections.store', ['book' => $book]), [
				'title' => $title,
				'content' => $content
			])
			->assertOk()
			->assertSeeText('новый контент');
	}

	public function testDontCountPrivateSections()
	{
		$book = factory(Book::class)->create();

		$section = factory(Section::class)
			->states('private')
			->create(['book_id' => $book->id]);

		$section2 = factory(Section::class)->states('accepted')
			->create(['book_id' => $book->id]);

		$book->refreshSectionsCount();

		$this->assertEquals(1, $book->sections_count);
	}

	public function testRefreshPrivateChaptersCountAfterUpdate()
	{
		$author = factory(Author::class)
			->states('with_author_manager', 'with_book_for_sale')
			->create();

		$user = $author->managers->first()->user;
		$book = $author->books->first();
		$book->create_user()->associate($user);
		$book->push();

		$title = $this->faker->realText(100);
		$content = '<p>' . $this->faker->realText(100) . '</p>';

		$section = $book->sections()->chapter()->orderBy('id', 'desc')->first();

		$this->actingAs($user)
			->patch(route('books.sections.update', ['book' => $book, 'section' => $section->inner_id]),
				[
					'title' => $title,
					'content' => $content,
					'status' => StatusEnum::Accepted,
				])
			->assertSessionHasNoErrors()
			->assertRedirect();

		$book->refresh();
		$this->assertEquals(1, $book->sections_count);
		$this->assertEquals(0, $book->private_chapters_count);

		$this->actingAs($user)
			->patch(route('books.sections.update', ['book' => $book, 'section' => $section->inner_id]),
				[
					'title' => $title,
					'content' => $content,
					'status' => StatusEnum::Private,
				])
			->assertSessionHasNoErrors()
			->assertRedirect();

		$book->refresh();
		$this->assertEquals(0, $book->sections_count);
		$this->assertEquals(1, $book->private_chapters_count);

		$this->actingAs($user)
			->patch(route('books.sections.update', ['book' => $book, 'section' => $section->inner_id]),
				[
					'title' => $title,
					'content' => $content,
					'status' => StatusEnum::Accepted,
				])
			->assertSessionHasNoErrors()
			->assertRedirect();

		$book->refresh();

		$this->assertEquals(1, $book->sections_count);
		$this->assertEquals(0, $book->private_chapters_count);
	}

	public function testSectionIndexPurchaseVariableInView()
	{
		$author = factory(Author::class)
			->states('with_author_manager_can_sell', 'with_book_for_sale_purchased')
			->create();

		$manager = $author->managers->first();
		$book = $author->books->first();
		$user = $manager->user;
		$buyer = $book->boughtUsers->first();
		$purchase = $book->purchases->where('buyer_user_id', auth()->id())->first();

		$this->assertTrue($book->isForSale());

		$this->actingAs($buyer)
			->get(route('books.sections.index', $book))
			->assertOk()
			->assertViewHas(['purchase' => $purchase]);

		$this->get(route('books.sections.index', $book))
			->assertOk()
			->assertViewHas(['purchase' => null]);

		$user2 = factory(User::class)->create();

		$this->actingAs($user2)
			->get(route('books.sections.index', $book))
			->assertOk()
			->assertViewHas(['purchase' => null]);
	}

	public function testCantViewPrivateBookSectionText()
	{
		$book = factory(Book::class)
			->states('private', 'with_section')
			->create();

		$section = $book->sections()->chapter()->first();

		$this->get(route('books.sections.show', ['book' => $book, 'section' => $section->inner_id]))
			->assertForbidden();
	}

	public function testCanViewSectionTextIfBookPublished()
	{
		$book = factory(Book::class)
			->states('accepted', 'with_section')
			->create();

		$section = $book->sections()->chapter()->first();

		$this->get(route('books.sections.show', ['book' => $book, 'section' => $section->inner_id]))
			->assertOk();
	}

	public function testSectionShowRouteIsNotFoundIfPageNotExists()
	{
		$book = factory(Book::class)
			->states('accepted', 'with_section')
			->create();

		$section = $book->sections()->chapter()->first();

		$this->get(route('books.sections.show', [
			'book' => $book,
			'section' => $section->inner_id,
			'page' => 1234
		]))
			->assertNotFound()
			->assertSeeText(__('section.book_page_was_not_found'))
			->assertSeeText(__('section.go_to_the_sections_index'));
	}

	public function testIsHigherThan()
	{
		$book = factory(Book::class)->create();

		$section1 = factory(Section::class)->states('chapter')->create(['book_id' => $book->id]);
		$subsection1 = factory(Section::class)->states('chapter')->create(['book_id' => $book->id]);
		$section1->appendNode($subsection1);

		$section2 = factory(Section::class)->states('chapter')->create(['book_id' => $book->id]);
		$subsection2 = factory(Section::class)->states('chapter')->create(['book_id' => $book->id]);
		$section2->appendNode($subsection2);

		$this->assertTrue($section1->isHigherThan($section2));
		$this->assertTrue($subsection1->isHigherThan($section2));
		$this->assertTrue($section1->isHigherThan($subsection2));
		$this->assertTrue($subsection1->isHigherThan($subsection2));
		$this->assertTrue($section2->isHigherThan($subsection2));

		$this->assertFalse($section2->isHigherThan($section1));
		$this->assertFalse($section2->isHigherThan($subsection1));

		$this->assertFalse($subsection2->isHigherThan($section2));
		$this->assertFalse($subsection2->isHigherThan($section1));
		$this->assertFalse($subsection2->isHigherThan($subsection1));

		$this->assertFalse($section2->isHigherThan($section1));
		$this->assertFalse($section2->isHigherThan($subsection1));

		$this->assertFalse($subsection1->isHigherThan($section1));

		$this->assertFalse($section1->isHigherThan($section1));
		$this->assertFalse($subsection1->isHigherThan($subsection1));
	}

	public function testIsLowerThan()
	{
		$book = factory(Book::class)->create();

		$section1 = factory(Section::class)->states('chapter')->create(['book_id' => $book->id]);
		$subsection1 = factory(Section::class)->states('chapter')->create(['book_id' => $book->id]);
		$section1->appendNode($subsection1);

		$section2 = factory(Section::class)->states('chapter')->create(['book_id' => $book->id]);
		$subsection2 = factory(Section::class)->states('chapter')->create(['book_id' => $book->id]);
		$section2->appendNode($subsection2);

		$this->assertTrue($subsection2->isLowerThan($section2));
		$this->assertTrue($subsection2->isLowerThan($section1));
		$this->assertTrue($subsection2->isLowerThan($subsection1));

		$this->assertTrue($section2->isLowerThan($section1));
		$this->assertTrue($section2->isLowerThan($subsection1));

		$this->assertTrue($subsection1->isLowerThan($section1));

		$this->assertFalse($section1->isLowerThan($section1));
		$this->assertFalse($subsection1->isLowerThan($subsection1));

		$this->assertFalse($section1->isLowerThan($section2));
		$this->assertFalse($subsection1->isLowerThan($section2));
		$this->assertFalse($section1->isLowerThan($subsection2));
		$this->assertFalse($subsection1->isLowerThan($subsection2));
		$this->assertFalse($section2->isLowerThan($subsection2));
	}

	public function testIsPaid()
	{
		$book = factory(Book::class)
			->states('on_sale')->create(['free_sections_count' => 3]);

		$section1 = factory(Section::class)->states('chapter')->create(['book_id' => $book->id]);
		$subsection1 = factory(Section::class)->states('chapter')->create(['book_id' => $book->id]);
		$section1->appendNode($subsection1);

		$section2 = factory(Section::class)->states('chapter')->create(['book_id' => $book->id]);
		$subsection2 = factory(Section::class)->states('chapter')->create(['book_id' => $book->id]);
		$section2->appendNode($subsection2);

		$this->assertFalse($section1->isPaid());
		$this->assertFalse($subsection1->isPaid());
		$this->assertFalse($section2->isPaid());
		$this->assertTrue($subsection2->isPaid());
	}
}
