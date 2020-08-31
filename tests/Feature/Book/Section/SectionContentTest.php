<?php

namespace Tests\Feature\Book\Section;

use App\Book;
use App\Section;
use Tests\TestCase;

class SectionContentTest extends TestCase
{
	public function testSetContentFactory()
	{
		$content = '<p>' . $this->faker->realText(300) . '</p>';

		$section = factory(Section::class)
			->create(['content' => $content]);

		$this->assertEquals($content, $section->getContent());
	}

	public function testSetContentAndGetContent()
	{
		$content = '<p>' . $this->faker->realText(300) . '</p>';

		$section = factory(Section::class)->create();
		$section->content = $content;
		$section->save();

		$section->refresh();

		$this->assertEquals($content, $section->getContent());
	}

	public function testCreateNewPagesIfTheContentHasChanged()
	{
		$content = '<p>' . $this->faker->realText(300) . '</p>';

		$section = factory(Section::class)->create(['content' => $content]);
		$section->refresh();

		$page_id = $section->pages()->first()->id;

		$newContent = '<p>' . $this->faker->realText(300) . '</p>';

		$section->content = $newContent;
		$section->save();
		$section->refresh();

		$this->assertEquals($newContent, $section->getContent());
		$this->assertNotEquals($page_id, $section->pages()->first()->id);
	}

	public function testDoNotCreateNewPagesIfTheContentHasNotChanged()
	{
		$content = '<p>' . $this->faker->realText(300) . '</p>';

		$section = factory(Section::class)->create();
		$section->content = $content;
		$section->save();
		$section->refresh();

		$page_id = $section->pages()->first()->id;

		$section->content = $content;
		$section->save();
		$section->refresh();

		$this->assertEquals($page_id, $section->pages()->first()->id);
	}

	public function testSetEmptyContent()
	{
		$content = '<p>' . $this->faker->realText(300) . '</p>';

		$section = factory(Section::class)
			->create(['content' => $content]);

		$this->assertEquals($content, $section->getContent());

		$section->content = '';
		$section->save();

		$this->assertEquals('', $section->getContent());
	}

	public function testAppendPrefixToIdsAndClasses()
	{
		config(['litlife.class_prefix' => 'prefix-']);

		$section = factory(Section::class)
			->create();

		$section->prefix = 'prefix-';

		$xhtml = '<p>текст <a name="some_id" href="">link</a> текст <span id="another_id">текст</span></p>';

		$section->content = $xhtml;
		$section->save();
		$section->refresh();

		$this->assertEquals('<p>текст <a href="" id="prefix-some_id">link</a> текст <span id="prefix-another_id">текст</span></p>',
			$section->getContent());

		$xhtml = '<div class="epigraph">текст</div>';

		$section->content = $xhtml;
		$section->save();
		$section->refresh();

		$this->assertEquals('<div class="prefix-epigraph">текст</div>',
			$section->getContent());
	}

	public function testAllowedClasses()
	{
		config(['litlife.class_prefix' => 'prefix-']);

		$section = factory(Section::class)
			->create();
		$section->prefix = config('litlife.class_prefix');

		$section->content = '<div class="epigraph">текст</div>';
		$section->save();
		$section->refresh();

		$this->assertEquals('<div class="prefix-epigraph">текст</div>', $section->getContent());

		$section->content = '<div class="text-author">текст</div>';
		$section->save();
		$section->refresh();

		$this->assertEquals('<div class="prefix-text-author">текст</div>', $section->getContent());

		$section->content = '<div class="stanza">текст</div>';
		$section->save();
		$section->refresh();

		$this->assertEquals('<div class="prefix-stanza">текст</div>', $section->getContent());

		$section->content = '<div class="subtitle">текст</div>';
		$section->save();
		$section->refresh();

		$this->assertEquals('<div class="prefix-subtitle">текст</div>', $section->getContent());

		$section->content = '<div class="poem">текст</div>';
		$section->save();
		$section->refresh();

		$this->assertEquals('<div class="prefix-poem">текст</div>', $section->getContent());

		$section->content = '<div class="title">текст</div>';
		$section->save();
		$section->refresh();

		$this->assertEquals('<div class="prefix-title">текст</div>', $section->getContent());

		$section->content = '<div class="empty-line">текст</div>';
		$section->save();
		$section->refresh();

		$this->assertEquals('<div class="prefix-empty-line">текст</div>', $section->getContent());

		$section->content = '<div class="annotation">текст</div>';
		$section->save();
		$section->refresh();

		$this->assertEquals('<div class="prefix-annotation">текст</div>', $section->getContent());

		$section->content = '<div class="not_allowed_class">текст</div>';
		$section->save();
		$section->refresh();

		$this->assertEquals('<div>текст</div>', $section->getContent());
	}

	public function testExternalLink()
	{
		config(['purify.settings.URI.Host' => null]);
		config(['purify.settings.URI.Munge' => null]);

		$section = factory(Section::class)->create();
		$section->content = '<p> <a href="http://example.com/test#u-section-1">test</a> </p>';
		$section->save();
		$section->refresh();

		$this->assertEquals('<p><a href="http://example.com/test#u-section-1">test</a></p>', $section->getContent());
	}

	public function testContentRightScheme()
	{
		$section = factory(Section::class)->create();
		$section->content = '<h4>текст</h4>текст<h4>текст</h4><a href="http://example.com">текст</a>';
		$section->save();
		$section->refresh();

		$this->assertEquals('<h4>текст</h4><p>текст</p><h4>текст</h4><p><a href="/away?url=http%3A%2F%2Fexample.com">текст</a></p>', $section->getContent());
	}

	public function testCheckEmptyTagsNotSelfClosing()
	{
		$book = factory(Book::class)->create();

		$section = factory(Section::class)
			->create([
				'book_id' => $book->id,
				'content' => '<p>текст </p><p><a href="#test"></a></p><p> текст</p>'
			])->fresh();

		$this->assertEquals('<p>текст</p><p><a href="#u-test"></a></p><p>текст</p>',
			$section->getContentHandeled());
	}

	public function testFirstDiv()
	{
		$content = '<div id="u-test"><div><h2>62</h2></div><p>текст</p><br/><a href="#u-test2">ссылка</a></div>';

		$book = factory(Book::class)->create();

		$section = factory(Section::class)
			->create([
				'book_id' => $book->id,
				'content' => $content
			])->fresh();

		$this->assertEquals($content, $section->getContent());

		$page = $section->pages()->first();

		$this->assertEquals(['u-section-1', 'u-test'], $page->getHtmlIds());
	}
}
