<?php

namespace Tests\Feature\Book\TextProcessing;

use App\Book;
use App\BookTextProcessing;
use App\Console\Commands\Book\BookTextWaitedProcessingCommand;
use App\Section;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class BookTextProcessingSplitIntoChaptersTest extends TestCase
{
	public function testSplitIntoChapters()
	{
		$book = Book::factory()->with_section()->create();
		$book->forbid_to_change = true;
		$book->save();

		$content = '<p>текст <strong>первой</strong> главы</p>' .
			'<p id="title2"><strong>Глава 2</strong></p>' .
			'<p>текст <strong>второй</strong> главы</p>' .
			'<p style="text-align:center" id="title3"><span><span><span><strong><span><span>ГЛАВА 3</span></span></strong></span></span></span></p>' .
			'<p>текст <strong>третьей</strong> главы</p>' .
			'<p style="text-align:center"><span><span><span><strong>  Эпилог</strong></span></span></span></p>' .
			'<p>текст эпилога</p>';

		$section = $book->sections()->first();
		$section->title = 'Глава 1';
		$section->content = $content;
		$section->save();
		$section->refresh();

		$processing = BookTextProcessing::factory()->create(['book_id' => $book->id, 'split_into_chapters' => true]);

		Artisan::call('book:text_waited_processing', ['latest_id' => $processing->id]);

		$processing->refresh();
		$section->refresh();

		$this->assertTrue($processing->isCompleted());

		$sections = $book->sections()->chapter()->defaultOrder()->get();

		$this->assertEquals(4, $sections->count());

		$section = $sections[0];

		$this->assertEquals('Глава 1', $section->title);
		$this->assertEquals('<p>текст <strong>первой</strong> главы</p>', $section->getContent());

		$section2 = $sections[1];

		$this->assertEquals('Глава 2', $section2->title);
		$this->assertEquals('<p>текст <strong>второй</strong> главы</p>', $section2->getContent());
		$this->assertEquals('u-title2', $section2->getTitleId());

		$section3 = $sections[2];

		$this->assertEquals('ГЛАВА 3', $section3->title);
		$this->assertEquals('<p>текст <strong>третьей</strong> главы</p>', $section3->getContent());
		$this->assertEquals('u-title3', $section3->getTitleId());

		$section4 = $sections[3];

		$this->assertEquals('Эпилог', $section4->title);
		$this->assertEquals('<p>текст эпилога</p>', $section4->getContent());
	}


	public function testSplitIntoChaptersTwoSections()
	{
		$book = Book::factory()->with_section()->create();
		$book->forbid_to_change = true;
		$book->save();

		$content = '<p>текст <strong>первой</strong> главы</p>' .
			'<p><strong>Глава 2</strong></p>' .
			'<p>текст <strong>второй</strong> главы</p>';

		$section = $book->sections()->first();
		$section->title = 'Глава 1';
		$section->content = $content;
		$section->save();
		$section->refresh();

		$content = '<p>текст <strong>третьей</strong> главы</p>' .
			'<p><strong>Глава 4</strong></p>' .
			'<p>текст <strong>четвертой</strong> главы</p>';

		$section2 = Section::factory()->create([
				'title' => 'Глава 3',
				'book_id' => $book->id
			]);
		$section2->content = $content;
		$section2->save();
		$section2->refresh();

		$processing = BookTextProcessing::factory()->create(['book_id' => $book->id, 'split_into_chapters' => true]);

		Artisan::call('book:text_waited_processing', ['latest_id' => $processing->id]);

		$processing->refresh();
		$section->refresh();

		$this->assertTrue($processing->isCompleted());

		$sections = $book->sections()->chapter()->defaultOrder()->get();

		$this->assertEquals(4, $sections->count());

		$section = $sections[0];

		$this->assertEquals('Глава 1', $section->title);
		$this->assertEquals('<p>текст <strong>первой</strong> главы</p>', $section->getContent());

		$section2 = $sections[1];

		$this->assertEquals('Глава 2', $section2->title);
		$this->assertEquals('<p>текст <strong>второй</strong> главы</p>', $section2->getContent());

		$section3 = $sections[2];

		$this->assertEquals('Глава 3', $section3->title);
		$this->assertEquals('<p>текст <strong>третьей</strong> главы</p>', $section3->getContent());

		$section4 = $sections[3];

		$this->assertEquals('Глава 4', $section4->title);
		$this->assertEquals('<p>текст <strong>четвертой</strong> главы</p>', $section4->getContent());
	}


	public function testSplitIntoChaptersIfEmptyText()
	{
		$book = Book::factory()->with_section()->create();
		$book->forbid_to_change = true;
		$book->save();

		$section = $book->sections()->first();
		$section->title = 'Глава 1';
		$section->content = '';
		$section->save();
		$section->refresh();

		$processing = BookTextProcessing::factory()->create(['book_id' => $book->id, 'split_into_chapters' => true]);

		Artisan::call('book:text_waited_processing', ['latest_id' => $processing->id]);

		$processing->refresh();
		$section->refresh();

		$this->assertTrue($processing->isCompleted());

		$this->assertEquals(1, $book->sections()->chapter()->count());
		$this->assertEquals('Глава 1', $section->title);
		$this->assertEquals('', $section->getContent());
	}

	public function testSplitIntoChaptersIfNoChapterTexts()
	{
		$book = Book::factory()->with_section()->create();
		$book->forbid_to_change = true;
		$book->save();

		$section = $book->sections()->first();
		$section->title = 'Глава 1';
		$section->content = '<p>текст</p><p>текст</p>';
		$section->save();
		$section->refresh();

		$processing = BookTextProcessing::factory()->create(['book_id' => $book->id, 'split_into_chapters' => true]);

		Artisan::call('book:text_waited_processing', ['latest_id' => $processing->id]);

		$processing->refresh();
		$section->refresh();

		$this->assertTrue($processing->isCompleted());

		$this->assertEquals(1, $book->sections()->chapter()->count());
		$this->assertEquals('Глава 1', $section->title);
		$this->assertEquals('<p>текст</p><p>текст</p>', $section->getContent());
	}

	public function testIsSectionTitle()
	{
		$command = new BookTextWaitedProcessingCommand();

		$this->assertTrue($command->isSectionTitle(' Предисловие'));
		$this->assertTrue($command->isSectionTitle(' эПилог  '));
		$this->assertTrue($command->isSectionTitle('&nbsp;Глава  3  '));
		$this->assertTrue($command->isSectionTitle(' Глава     100  '));
		$this->assertTrue($command->isSectionTitle('&nbsp;  Глава &nbsp; 3  '));
		$this->assertTrue($command->isSectionTitle('&nbsp; 4  Глава &nbsp;  '));
		$this->assertTrue($command->isSectionTitle('Глава 13. Текст и прочее'));
		$this->assertTrue($command->isSectionTitle('Глава пятая'));
		$this->assertTrue($command->isSectionTitle('Глава сто пятнадцатая'));
		$this->assertTrue($command->isSectionTitle('Глава сто-пятнадцатая'));
		$this->assertTrue($command->isSectionTitle('Глава I'));
		$this->assertTrue($command->isSectionTitle('Глава V &nbsp;'));
		$this->assertTrue($command->isSectionTitle('Глава XI'));
		$this->assertTrue($command->isSectionTitle('Глава XXI'));
		$this->assertTrue($command->isSectionTitle('Глава XCIV'));
		$this->assertTrue($command->isSectionTitle('Глава LVII'));
		$this->assertTrue($command->isSectionTitle('Глава LVII'));
		$this->assertTrue($command->isSectionTitle('&nbsp; Глава XD'));
		$this->assertTrue($command->isSectionTitle('Глава LVII. Текст и прочее'));
		$this->assertTrue($command->isSectionTitle('Часть 1. Название'));
		$this->assertTrue($command->isSectionTitle('Часть LXI. Название'));
		$this->assertTrue($command->isSectionTitle('Часть десятая. Название'));
		$this->assertTrue($command->isSectionTitle('Часть тридцать восьмая. Название'));
		$this->assertTrue($command->isSectionTitle(' Пролог'));
		$this->assertTrue($command->isSectionTitle(' 2. НАЗВАНИЕ '));
		$this->assertTrue($command->isSectionTitle(' Пролог. Часть 1 '));
		$this->assertTrue($command->isSectionTitle(' Пролог. Часть 2 '));
		$this->assertTrue($command->isSectionTitle(' Пролог ЧастЬ 5 '));
		$this->assertTrue($command->isSectionTitle('ГЛАВА ЧЕТЫРНАДЦАТАЯ'));
		$this->assertTrue($command->isSectionTitle('ГЛАВА ДВАДЦАТАЯ'));
		$this->assertTrue($command->isSectionTitle('ГЛАВА ТРИДЦАТАЯ'));
		$this->assertTrue($command->isSectionTitle('ГЛАВА СОРОКОВАЯ'));
		$this->assertTrue($command->isSectionTitle('ГЛАВА ПЯТИДЕСЯТАЯ'));
		$this->assertTrue($command->isSectionTitle('ГЛАВА ШЕСТИДЕСЯТАЯ'));
		$this->assertTrue($command->isSectionTitle('ГЛАВА СЕМИДЕСЯТАЯ'));
		$this->assertTrue($command->isSectionTitle('ГЛАВА ВОСМИДЕСЯТАЯ'));
		$this->assertTrue($command->isSectionTitle('ГЛАВА ДЕВЯНОСТАЯ'));
		$this->assertTrue($command->isSectionTitle('ГЛАВА СОТАЯ'));
		$this->assertTrue($command->isSectionTitle('ГЛАВА ДВАДЦАТЬ ЧЕТВЁРТАЯ'));
		$this->assertTrue($command->isSectionTitle('ГЛАВА СТО ПЕРВАЯ'));
		$this->assertTrue($command->isSectionTitle('ЧАСТЬ ПЕРВАЯ. ЗАМКИ В ТУМАНЕ'));
		$this->assertTrue($command->isSectionTitle('ГЛАВА ДВУХСОТАЯ'));
		$this->assertTrue($command->isSectionTitle('ГЛАВА ШЕСТИСОТАЯ'));
		$this->assertTrue($command->isSectionTitle('ГЛАВА СЕМНАДЦАТАЯ'));


		$this->assertFalse($command->isSectionTitle('Глава3'));
		$this->assertFalse($command->isSectionTitle('эпило'));
		$this->assertFalse($command->isSectionTitle('Эпилг'));
		$this->assertFalse($command->isSectionTitle('4 Глава 5'));
		$this->assertFalse($command->isSectionTitle('Глава 13. Текст текст текст текст текст текст текст текст текст' .
			' текст текст текст текст текст текст текст текст текст текст текст текст текст текст текст текст текст текст' .
			' текст текст текст текст текст текст текст текст и прочее'));
		$this->assertFalse($command->isSectionTitle('Глава текст текст текст текст текст текст текст текст текст текст текст пятнадцатая'));
		$this->assertFalse($command->isSectionTitle('&nbsp; Глава XXTXXX'));
		$this->assertFalse($command->isSectionTitle('Глава XXJXXX &nbsp;'));
		$this->assertFalse($command->isSectionTitle('Глава LVII. Текст текст текст текст текст текст текст текст текст' .
			' текст текст текст текст текст текст текст текст текст текст текст текст текст текст текст текст текст текст' .
			' текст текст текст текст текст текст текст текст и прочее'));
		$this->assertFalse($command->isSectionTitle('Часть 1. Текст текст текст текст текст текст текст текст текст' .
			' текст текст текст текст текст текст текст текст текст текст текст текст текст текст текст текст текст текст' .
			' текст текст текст текст текст текст текст текст и прочее'));
		$this->assertFalse($command->isSectionTitle('Часть CXI. Текст текст текст текст текст текст текст текст текст' .
			' текст текст текст текст текст текст текст текст текст текст текст текст текст текст текст текст текст текст' .
			' текст текст текст текст текст текст текст текст и прочее'));
		$this->assertFalse($command->isSectionTitle('3. Название'));
	}

	public function testDontCreateNewPagesIfNoChangesAfterSplitIntoChapters()
	{
		$book = Book::factory()->with_section()->create();
		$book->forbid_to_change = true;
		$book->save();

		$section = $book->sections()->first();
		$section->content = '<p>текст</p>';
		$section->save();

		$page_id = $section->pages()->first()->id;

		$processing = BookTextProcessing::factory()->create(['book_id' => $book->id, 'split_into_chapters' => true]);

		Artisan::call('book:text_waited_processing', ['latest_id' => $processing->id]);

		$processing->refresh();

		$this->assertTrue($processing->isCompleted());

		$this->assertEquals($page_id, $section->pages()->first()->id);
	}

	public function testSplitIntoChapters2()
	{
		$book = Book::factory()->with_section()->create();
		$book->forbid_to_change = true;
		$book->save();

		$content = '<p><strong>Часть 1</strong></p>' .
			'<div class="u-empty-line">&nbsp;</div>' .
			'<p><strong>Пролог</strong></p>' .
			'<div class="u-empty-line">&nbsp;</div>' .
			'<p>Текст пролога</p>' .
			'<p><strong>Часть 2</strong></p>' .
			'<p><strong>Текст второй части</strong></p>';

		$section = $book->sections()->first();
		$section->title = 'Название книги';
		$section->content = $content;
		$section->save();
		$section->refresh();

		$processing = BookTextProcessing::factory()->create(['book_id' => $book->id, 'split_into_chapters' => true]);

		Artisan::call('book:text_waited_processing', ['latest_id' => $processing->id]);

		$processing->refresh();
		$section->refresh();

		$this->assertTrue($processing->isCompleted());
		$sections = $book->sections()->chapter()->defaultOrder()->get();
		$this->assertEquals(4, $sections->count());

		$section0 = $sections[0];

		$this->assertEquals('Название книги', $section0->title);
		$this->assertEquals('', $section0->getContent());

		$section1 = $sections[1];

		$this->assertEquals('Часть 1', $section1->title);
		$this->assertEquals('<div class="u-empty-line"> </div>',
			$section1->getContent());

		$section2 = $sections[2];

		$this->assertEquals('Пролог', $section2->title);
		$this->assertEquals('<div class="u-empty-line"> </div><p>Текст пролога</p>',
			$section2->getContent());

		$section3 = $sections[3];

		$this->assertEquals('Часть 2', $section3->title);
		$this->assertEquals('<p><strong>Текст второй части</strong></p>', $section3->getContent());
	}

	public function testSplitIntoChaptersEpiloguePrologue()
	{
		$book = Book::factory()->with_section()->create();
		$book->forbid_to_change = true;
		$book->save();

		$content = '<p><strong>Пролог</strong></p>' .
			'<p>Текст пролога</p>' .
			'<p><strong>Эпилог</strong></p>' .
			'<p>Текст эпилога</p>';

		$section = $book->sections()->first();
		$section->title = 'Пролог';
		$section->content = $content;
		$section->save();
		$section->refresh();

		$processing = BookTextProcessing::factory()->create(['book_id' => $book->id, 'split_into_chapters' => true]);

		Artisan::call('book:text_waited_processing', ['latest_id' => $processing->id]);

		$processing->refresh();
		$section->refresh();

		$this->assertTrue($processing->isCompleted());
		$sections = $book->sections()->chapter()->defaultOrder()->get();
		$this->assertEquals(2, $sections->count());

		$section = $sections[0];

		$this->assertEquals('Пролог', $section->title);
		$this->assertEquals('<p>Текст пролога</p>', $section->getContent());

		$section2 = $sections[1];

		$this->assertEquals('Эпилог', $section2->title);
		$this->assertEquals('<p>Текст эпилога</p>', $section2->getContent());
	}

	public function testSplitIntoPartsEvenIfThereIsNoTextBetweenTheChapters()
	{
		$book = Book::factory()->create();

		$section = factory(Section::class)
			->states('chapter')
			->create([
				'book_id' => $book->id,
				'title' => 'ЧАСТЬ ПЕРВАЯ. ЗАМКИ В ТУМАНЕ',
				'content' => '<p>ГЛАВА ПЕРВАЯ</p><p>текст текст</p>'
			]);

		$this->assertEquals('ЧАСТЬ ПЕРВАЯ. ЗАМКИ В ТУМАНЕ', $section->title);
		$this->assertEquals('<p>ГЛАВА ПЕРВАЯ</p><p>текст текст</p>', $section->getContent());

		$processing = BookTextProcessing::factory()->create(['book_id' => $book->id, 'split_into_chapters' => true]);

		Artisan::call('book:text_waited_processing', ['latest_id' => $processing->id]);

		$processing->refresh();
		$section->refresh();

		$this->assertTrue($processing->isCompleted());

		$sections = $book->sections()
			->chapter()
			->defaultOrder()
			->get();

		$this->assertEquals('ЧАСТЬ ПЕРВАЯ. ЗАМКИ В ТУМАНЕ', $sections[0]->title);
		$this->assertEquals('', $sections[0]->getContent());
		$this->assertEquals('ГЛАВА ПЕРВАЯ', $sections[1]->title);
		$this->assertEquals('<p>текст текст</p>', $sections[1]->getContent());
	}
	/*
		public function test()
		{
			$book = Book::factory()->create();

			$content = '<p><b>Название 1</b></p><p>Текст 1</p><p><b>Название 2</b></p><p>Текст 2</p>';

			$section = factory(Section::class)
				->states('chapter')
				->create([
					'book_id' => $book->id,
					'title' => 'тест',
					'content' => $content
				]);

			$processing = BookTextProcessing::factory()->create(['book_id' => $book->id, 'split_into_chapters' => true]);

			Artisan::call('book:text_waited_processing', ['latest_id' => $processing->id]);

			$processing->refresh();
			$section->refresh();

			$this->assertTrue($processing->isCompleted());

			$sections = $book->sections()
				->chapter()
				->defaultOrder()
				->get();

			//$this->assertEquals('Название 1', $sections[0]->title);
			$this->assertEquals('Текст 1', $sections[0]->getContent());
			$this->assertEquals('Название 2', $sections[1]->title);
			$this->assertEquals('Текст 2', $sections[1]->getContent());
		}
		*/
}
