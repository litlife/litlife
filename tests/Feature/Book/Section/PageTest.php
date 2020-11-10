<?php

namespace Tests\Feature\Book\Section;

use App\Section;
use App\User;
use DOMDocument;
use Tests\TestCase;

class PageTest extends TestCase
{
	/**
	 * A basic test example.
	 *
	 * @return void
	 */
	public function testSetDom()
	{
		$section = Section::factory()->create();

		$xhtml = '<p>текст текст</p>';

		$dom = new DOMDocument();
		$dom->loadHTML('<meta http-equiv="Content-Type" content="text/html; charset=utf-8">' . $xhtml);

		$page = $section->pages()->first();
		$page->setDOM($dom);
		$page->save();
		$page->refresh();

		$this->assertEquals($xhtml, $page->content);
	}

	public function testIdsOnCreate()
	{
		$section = Section::factory()->create();
		$page = $section->pages()->first();

		$xhtml = '<p>текст текст <a id="some_id" href="">link</a></p>';

		$page->content = $xhtml;
		$page->save();

		$this->assertContains('some_id', $page->html_tags_ids);
	}

	public function testIdsOnUpdate()
	{
		$section = Section::factory()->create();
		$page = $section->pages()->first();

		$xhtml = '<p>текст текст <a id="old_id" href="">link</a></p>';

		$page->content = $xhtml;
		$page->save();

		$page->content = '<p>текст текст <a id="new_id" href="">link</a></p>';
		$page->save();
		$page->refresh();

		$this->assertNotContains('some_id', $page->html_tags_ids);
		$this->assertContains('new_id', $page->html_tags_ids);
	}

	public function testNoErrorIfGuestSeePage()
	{
		$section = Section::factory()->create();
		$page = $section->pages()->first();
		$book = $page->book;
		$book->statusAccepted();
		$book->readAccessEnable();
		$book->save();

		$this->assertEquals($page->book->id, $page->section->book->id);
		$this->assertTrue($page->book->isReadAccess());

		$this->get(route('books.sections.show', ['book' => $page->book, 'section' => $page->section->inner_id]))
			->assertOk();
	}

	public function testMinimumNumberOfCharactersPerPageToDisplayAds()
	{
		config(['litlife.minimum_number_of_characters_per_page_to_display_ads' => 1000]);

		$user = User::factory()->create();

		$section = Section::factory()->create();
		$page = $section->pages()->first();

		$page->character_count = 1100;
		$page->save();
		$page->refresh();

		$this->assertTrue($user->can('display_ads', $page));

		$page->character_count = 900;
		$page->save();
		$page->refresh();

		$this->assertFalse($user->can('display_ads', $page));
	}
}
