<?php

namespace Tests\Feature\Book\Section;

use App\Book;
use App\Section;
use Tests\TestCase;

class SectionTitleTest extends TestCase
{
	public function testSetNameUntitledIfEmptyContent()
	{
		$book = factory(Book::class)
			->create();

		$section = new Section;
		$section->scoped(['book_id' => $book->id, 'type' => 'section']);
		$section->book_id = $book->id;
		$section->save();

		$this->assertEquals(__('section.untitled'), $section->title);
	}

	public function testRemoveTitleFromTextV1()
	{
		$title = $this->faker->realText(50);

		$content = '<p>' . $this->faker->realText(200) . '</p>';

		$section = factory(Section::class)
			->create([
				'title' => $title,
				'content' => '<h1 class="u-title">' . $title . '</h1>' . $content
			])->fresh();

		$this->assertEquals($content, $section->getContent());
	}

	public function testRemoveTitleFromTextV2()
	{
		$title = $this->faker->realText(50);

		$content = '<p>' . $this->faker->realText(200) . '</p>';

		$section = factory(Section::class)
			->create([
				'title' => $title,
				'content' => '<h1>' . $title . '</h1>' . $content
			])->fresh();

		$this->assertEquals($content, $section->getContent());
	}

	public function testRemoveTitleFromTextV3()
	{
		$title = $this->faker->realText(50);

		$content = '<p>текст</p><p>текст</p><p>текст</p><p>текст</p>' . '<h1>' . $title . '</h1>';

		$section = factory(Section::class)
			->create([
				'title' => $title,
				'content' => '<h1>' . $title . '</h1>' . $content
			])->fresh();

		$this->assertEquals($content, $section->getContent());
	}

	public function testAutoTitleIfTagStrongInsideTagP()
	{
		$book = factory(Book::class)
			->create();

		$content = '
		<p><b>Название   главы</b></p>

<div class="u-empty-line">&nbsp;</div>
<p>текст второй главы</p>
';

		$section = new Section;
		$section->scoped(['book_id' => $book->id, 'type' => 'section']);
		$section->content = $content;

		$this->assertEquals('Название главы', $section->title);

		$section->book_id = $book->id;
		$section->save();
		$section->refresh();

		$this->assertEquals('Название главы', $section->title);
		$this->assertEquals('<div class="u-empty-line"> </div><p>текст второй главы</p>', $section->getContent());
	}

	public function testAutoTitleForHeaderTags()
	{
		$book = factory(Book::class)
			->create();

		$content = '
		<h6>Название   главы</h6>

<div class="u-empty-line">&nbsp;</div>
<p>текст второй главы</p>
';

		$section = new Section;
		$section->scoped(['book_id' => $book->id, 'type' => 'section']);
		$section->content = $content;

		$this->assertEquals('Название главы', $section->title);

		$section->book_id = $book->id;
		$section->save();
		$section->refresh();

		$this->assertEquals('Название главы', $section->title);
		$this->assertEquals('<div class="u-empty-line"> </div><p>текст второй главы</p>', $section->getContent());
	}

	public function testDontCreateTitleIfParagraph()
	{
		$book = factory(Book::class)
			->create();

		$content = '
		<p>Название главы</p>
<div class="u-empty-line">&nbsp;</div>
<p>текст второй главы</p>
';

		$section = new Section;
		$section->scoped(['book_id' => $book->id, 'type' => 'section']);
		$section->content = $content;

		$this->assertNull($section->title);
	}

	public function testTitleId()
	{
		$section = factory(Section::class)->create();
		$section->setTitleId('title2');
		$section->content = 'текст';
		$section->save();
		$section->refresh();

		$this->assertEquals('u-title2', $section->getTitleId());

		$page = $section->pages()->first();

		$this->assertEquals(['u-section-1', 'u-title2'], $page->getHtmlIds());
	}

	public function testRemoveH6TagIfItMatchesTheChapterTitle()
	{
		$book = factory(Book::class)
			->create();

		$content = '
		<h6 id="title"> Название    главы</h6>

<div class="u-empty-line">&nbsp;</div>
<p>текст второй главы</p>
';

		$section = new Section;
		$section->scoped(['book_id' => $book->id, 'type' => 'section']);
		$section->title = '  Название главы';
		$section->content = $content;
		$section->book_id = $book->id;
		$section->save();
		$section->refresh();

		$this->assertEquals('Название главы', $section->title);
		$this->assertEquals('<div class="u-empty-line"> </div><p>текст второй главы</p>', $section->getContent());
		$this->assertEquals('u-title', $section->getTitleId());
	}

	public function testRemoveH1TagIfItMatchesTheChapterTitle()
	{
		$book = factory(Book::class)
			->create();

		$content = '
		<h1 id="title">  Название главы  </h1>

<div class="u-empty-line">&nbsp;</div>
<p>текст второй главы</p>
';

		$section = new Section;
		$section->scoped(['book_id' => $book->id, 'type' => 'section']);
		$section->title = 'Название главы  ';
		$section->content = $content;
		$section->book_id = $book->id;
		$section->save();
		$section->refresh();

		$this->assertEquals('Название главы', $section->title);
		$this->assertEquals('<div class="u-empty-line"> </div><p>текст второй главы</p>', $section->getContent());
		$this->assertEquals('u-title', $section->getTitleId());
	}

	public function testRemovePTagIfItMatchesTheChapterTitle()
	{
		$book = factory(Book::class)
			->create();

		$content = '
		<p>  Название главы  </p>

<div class="u-empty-line">&nbsp;</div>
<p>текст второй главы</p>
';

		$section = new Section;
		$section->scoped(['book_id' => $book->id, 'type' => 'section']);
		$section->title = '  Название главы';
		$section->content = $content;
		$section->book_id = $book->id;
		$section->save();
		$section->refresh();

		$this->assertEquals('Название главы', $section->title);
		$this->assertEquals('<div class="u-empty-line"> </div><p>текст второй главы</p>', $section->getContent());
		$this->assertEquals(null, $section->getTitleId());
	}

	public function testDontRemovePTagIfItMatchesTheChapterTitleWithId()
	{
		$book = factory(Book::class)
			->create();

		$content = '<p id="title">Название главы</p>';

		$section = new Section;
		$section->scoped(['book_id' => $book->id, 'type' => 'section']);
		$section->title = 'Название главы';
		$section->content = $content;
		$section->book_id = $book->id;
		$section->save();
		$section->refresh();

		$this->assertEquals('Название главы', $section->title);
		$this->assertEquals('', $section->getContent());
		$this->assertEquals('u-title', $section->getTitleId());
	}

	public function testTitleInsideDivTag()
	{
		$title = 'Глава первая    Имя главы';

		$content = '<div id="test"><h4>Глава первая</h4><h4>Имя главы</h4></div><p>текст</p>';

		$section = factory(Section::class)
			->create([
				'title' => $title,
				'content' => $content
			]);

		$this->assertEquals('Глава первая Имя главы', $section->title);
		$this->assertEquals('<p>текст</p>', $section->getContent());
		$this->assertEquals('u-test', $section->getTitleId());
	}

	public function testTitleIdInsideDivTag()
	{
		$title = 'Глава первая Имя главы';

		$content = '<div><h4 id="test">Глава первая</h4><h4>Имя главы</h4></div><p>текст</p>';

		$section = factory(Section::class)
			->create([
				'title' => $title,
				'content' => $content
			]);

		$this->assertEquals($title, $section->title);
		$this->assertEquals('<p>текст</p>', $section->getContent());
		$this->assertEquals('u-test', $section->getTitleId());
	}

	public function testRemoveFirstParagraphIfTextInsideSameAsTitle()
	{
		$content = '<p><b>Эпилог</b></p><p>Текст эпилога</p>';

		$section = factory(Section::class)
			->create([
				'title' => 'Эпилог',
				'content' => $content
			]);

		$this->assertEquals('Эпилог', $section->title);
		$this->assertEquals('<p>Текст эпилога</p>', $section->getContent());
	}

	public function testDontRemoveFirstParagraph()
	{
		$content = '<p><b>текст</b></p><p>текст</p>';

		$section = factory(Section::class)
			->create([
				'title' => 'Название главы',
				'content' => $content
			]);

		$this->assertEquals('Название главы', $section->title);
		$this->assertEquals('<p><b>текст</b></p><p>текст</p>', $section->getContent());
	}

	public function testDeleteTextFromAChapterThatMatchesTheTitleText()
	{
		$content = '<p><i>Текст первой главы</i></p>';

		$section = factory(Section::class)
			->create([
				'title' => 'Текст первой главы'
			]);
		$section->content = $content;
		$section->save();

		$this->assertEquals('Текст первой главы', $section->title);
		$this->assertEquals('', $section->getContent());


		$content = '<p><b>Текст второй</b></p><p><b>главы</b></p>';

		$section = factory(Section::class)
			->create([
				'title' => 'Текст второй',
			]);
		$section->content = $content;
		$section->save();

		$this->assertEquals('Текст второй', $section->title);
		$this->assertEquals('<p><b>главы</b></p>', $section->getContent());


		$content = '<p>Текст четвертой</p><p>главы</p>';

		$section = factory(Section::class)
			->create([
				'title' => 'Текст четвертой'
			]);
		$section->content = $content;
		$section->save();

		$this->assertEquals('Текст четвертой', $section->title);
		$this->assertEquals('<p>главы</p>', $section->getContent());
	}

	public function testDontRemoveFirstBoldParagraphIfSectionIsAnnotation()
	{
		$content = '<p><b>текст</b></p><p>текст</p>';

		$section = factory(Section::class)
			->states('annotation')
			->create([
				'title' => null,
				'content' => $content
			]);

		$this->assertEquals(__('section.untitled'), $section->title);
		$this->assertEquals('<p><b>текст</b></p><p>текст</p>', $section->getContent());
	}

	public function testDontRemoveImageWithStrongTag()
	{
		$book = factory(Book::class)
			->create();

		$content = '
		<p><b><img src="image.png" /></b></p>

<div class="u-empty-line">&nbsp;</div>
<p>текст второй главы</p>
<p>еще текст второй главы</p>
';

		$section = new Section;
		$section->scoped(['book_id' => $book->id, 'type' => 'section']);
		$section->content = $content;
		$section->book_id = $book->id;
		$section->save();
		$section->refresh();

		$this->assertEquals(__('section.untitled'), $section->title);
		$this->assertEquals('<p><b></b></p><div class="u-empty-line"> </div><p>текст второй главы</p><p>еще текст второй главы</p>', $section->getContent());
	}
}