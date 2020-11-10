<?php

namespace Tests\Feature\Book\Section;

use App\Attachment;
use App\Book;
use App\Section;
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

		$book = Book::factory()->create([
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

	public function testCreateChild()
	{
		$book = Book::factory()->create([
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

	public function testFulltextSearch()
	{
		$author = Section::FulltextSearch('Время&—&детство!')->limit(5)->get();

		$this->assertTrue(true);
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

		$section = Section::factory()->create();
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

	public function testIsChangedMethod()
	{
		$section = Section::factory()->create();

		$this->assertTrue($section->isChanged('character_count'));

		$section = Section::findOrFail($section->id);

		$character_count = $section->character_count;
		$section->character_count = $character_count;
		$section->save();

		$this->assertFalse($section->isChanged('character_count'));

		$section = Section::findOrFail($section->id);

		$this->assertFalse($section->isChanged('character_count'));


		$book = Book::factory()->create();

		$section = new Section();
		$section->fill([
			'title' => $this->faker->realText(100),
			'content' => $this->faker->realText(100),
		]);

		$this->assertTrue($section->isChanged('character_count'));

		$book->sections()->save($section);
	}

	public function testXHTMLUsed()
	{
		$section = Section::factory()->create();

		$book = $section->book;

		$attachment = Attachment::factory()->create(['book_id' => $book->id]);

		$xhtml = '<p>текст <img src="' . $attachment->url . '" alt="test.jpg"/> текст</p>';

		$section->content = $xhtml;
		$section->save();
		$section->refresh();

		$this->assertEquals($xhtml, $section->getContent());
		$this->assertEquals(10, $section->character_count);
	}

	public function testGetFirstTag()
	{
		$section = Section::factory()->create();

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

	public function testInnerId()
	{
		$book = Book::factory()->create();

		$section = Section::factory()->create(['book_id' => $book->id]);

		$section2 = Section::factory()->create(['book_id' => $book->id]);

		$section->refresh();
		$section2->refresh();

		$this->assertEquals(1, $section->inner_id);
		$this->assertEquals(2, $section2->inner_id);
	}

	public function testEmptyContent()
	{
		$section = Section::factory()->create();
		$section->content = '';
		$section->save();
		$section->refresh();

		$this->assertEquals(0, $section->pages()->count());
		$this->assertEquals('', $section->getContent());
	}

	public function testDontCountPrivateSections()
	{
		$book = Book::factory()->create();

		$section = Section::factory()->private()->create();

		$section2 = Section::factory()->accepted()->create();

		$book->refreshSectionsCount();

		$this->assertEquals(1, $book->sections_count);
	}
}
