<?php

namespace Tests\Feature\Book\TextProcessing;

use App\Book;
use App\BookTextProcessing;
use App\Console\Commands\Book\BookTextWaitedProcessingCommand;
use App\Notifications\BookTextProcessingCompleteNotification;
use App\Section;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class BookTextProcessingCommandTest extends TestCase
{
	public function testStatusChanged()
	{
		Notification::fake();

		$book = factory(Book::class)
			->create();

		$processing = factory(BookTextProcessing::class)->create(['book_id' => $book->id]);
		$book->forbid_to_change = true;
		$book->save();

		Artisan::call('book:text_waited_processing', ['latest_id' => $processing->id]);

		$processing->refresh();
		$book->refresh();

		$this->assertTrue($processing->isCompleted());
		$this->assertFalse($book->forbid_to_change);
		$this->assertTrue($book->need_create_new_files);

		Notification::assertSentTo(
			$processing->create_user,
			BookTextProcessingCompleteNotification::class,
			function ($notification, $channels) use ($processing) {

				$this->assertContains('database', $channels);

				$array = $notification->toArray($processing->create_user);

				$this->assertEquals(__('notification.book_text_processing_complete.line', ['book_title' => $processing->book->title]), $array['title']);
				$this->assertEquals(__('notification.book_text_processing_complete.subject'), $array['description']);
				$this->assertEquals(route('books.show', $processing->book), $array['url']);

				return $notification->processing->id == $processing->id;
			}
		);
	}

	public function testRemoveExtraSpaces()
	{
		$book = factory(Book::class)
			->states('with_section')
			->create();
		$book->forbid_to_change = true;
		$book->save();

		$section = $book->sections()->first();
		$section->content = '<p>  &nbsp; &nbsp; &nbsp; - текст <strong>текст</strong></p><p>⠀⠀⠀⠀текст</p>';
		$section->save();

		$processing = factory(BookTextProcessing::class)
			->create(['book_id' => $book->id, 'remove_extra_spaces' => true]);

		Artisan::call('book:text_waited_processing', ['latest_id' => $processing->id]);

		$processing->refresh();
		$section->refresh();

		$this->assertTrue($processing->isCompleted());

		$this->assertEquals('<p> - текст <strong>текст</strong></p><p> текст</p>', $section->getContent());
	}

	public function testRemoveBold()
	{
		$book = factory(Book::class)
			->states('with_section')
			->create();
		$book->forbid_to_change = true;
		$book->save();

		$section = $book->sections()->first();
		$section->content = '<p>текст <strong><em><strong>текст</strong></em></strong> <s>текст</s></p>';
		$section->save();
		$section->refresh();

		$processing = factory(BookTextProcessing::class)
			->create(['book_id' => $book->id, 'remove_bold' => true]);

		Artisan::call('book:text_waited_processing', ['latest_id' => $processing->id]);

		$processing->refresh();
		$section->refresh();

		$this->assertTrue($processing->isCompleted());

		$this->assertEquals('<p>текст <em>текст</em> <s>текст</s></p>', $section->getContent());
	}

	public function testRemoveBoldAndRemoveExtraSpaces()
	{
		$book = factory(Book::class)
			->states('with_section')
			->create();
		$book->forbid_to_change = true;
		$book->save();

		$section = $book->sections()->first();
		$section->content = '<p>  &nbsp; &nbsp; &nbsp; - текст <strong>текст</strong></p>';
		$section->save();
		$section->refresh();

		$processing = factory(BookTextProcessing::class)
			->create(['book_id' => $book->id, 'remove_extra_spaces' => true, 'remove_bold' => true]);

		Artisan::call('book:text_waited_processing', ['latest_id' => $processing->id]);

		$processing->refresh();
		$section->refresh();

		$this->assertTrue($processing->isCompleted());

		$this->assertEquals('<p> - текст текст</p>', $section->getContent());
	}

	public function testDontCreateNewPagesIfNoChangesAfterRemoveBold()
	{
		$book = factory(Book::class)
			->states('with_section')
			->create();
		$book->forbid_to_change = true;
		$book->save();

		$section = $book->sections()->first();
		$section->content = '<p>текст</p>';
		$section->save();

		$page_id = $section->pages()->first()->id;

		$processing = factory(BookTextProcessing::class)
			->create(['book_id' => $book->id, 'remove_bold' => true]);

		Artisan::call('book:text_waited_processing', ['latest_id' => $processing->id]);

		$processing->refresh();

		$this->assertTrue($processing->isCompleted());

		$this->assertEquals($page_id, $section->pages()->first()->id);
	}

	public function testDontCreateNewPagesIfNoChangesAfterRemoveExtraSpaces()
	{
		$book = factory(Book::class)
			->states('with_section')
			->create();
		$book->forbid_to_change = true;
		$book->save();

		$section = $book->sections()->first();
		$section->content = '<p>текст</p>';
		$section->save();

		$page_id = $section->pages()->first()->id;

		$processing = factory(BookTextProcessing::class)
			->create(['book_id' => $book->id, 'remove_extra_spaces' => true]);

		Artisan::call('book:text_waited_processing', ['latest_id' => $processing->id]);

		$processing->refresh();

		$this->assertTrue($processing->isCompleted());

		$this->assertEquals($page_id, $section->pages()->first()->id);
	}

	public function testSplitIntoChaptersRemoveExtraSpacesRemoveBold()
	{
		$book = factory(Book::class)
			->states('with_section')
			->create();
		$book->forbid_to_change = true;
		$book->save();

		$content = '<p>      текст <strong>первой</strong> главы</p>' .
			'<p><strong>Глава 2</strong></p>' .
			'<p>      текст <strong>второй</strong> главы</p>';

		$section = $book->sections()->first();
		$section->title = 'Глава 1';
		$section->content = $content;
		$section->save();
		$section->refresh();

		$processing = factory(BookTextProcessing::class)
			->create([
				'book_id' => $book->id,
				'split_into_chapters' => true,
				'remove_extra_spaces' => true,
				'remove_bold' => true
			]);

		Artisan::call('book:text_waited_processing', ['latest_id' => $processing->id]);

		$processing->refresh();
		$section->refresh();

		$this->assertTrue($processing->isCompleted());

		$sections = $book->sections()->chapter()->defaultOrder()->get();

		$this->assertEquals(2, $sections->count());

		$section = $sections[0];

		$this->assertEquals('Глава 1', $section->title);
		$this->assertEquals('<p>текст первой главы</p>', $section->getContent());

		$section2 = $sections[1];

		$this->assertEquals('Глава 2', $section2->title);
		$this->assertEquals('<p>текст второй главы</p>', $section2->getContent());
	}

	public function testRemoveStrongTag()
	{
		$command = new BookTextWaitedProcessingCommand();

		$content = '<div id="u-test"><div><h2>1</h2></div><p>текст</p><br/><a href="#u-test2">ссылка</a></div>';

		$section = new Section;
		$section->content = $command->removeStrongTag($content);

		$this->assertEquals($section->saveXML(), $content);
	}

	public function testBrToParagraph()
	{
		$command = new BookTextWaitedProcessingCommand();

		$content = '<p><br>текст<br>текст<br>текст<br>&nbsp;</p>';

		$section = new Section;
		$section->content = $command->brToParagraph($content);

		$this->assertEquals('<p>текст</p><p>текст</p><p>текст</p><p> </p>', $section->saveXML());
	}

	public function testConvertNewLinesTo()
	{
		$book = factory(Book::class)
			->states('with_section')
			->create();
		$book->forbid_to_change = true;
		$book->save();

		$section = $book->sections()->first();
		$section->content = '<br>текст<br>текст<br>текст<br>';
		$section->save();
		$section->refresh();

		$processing = factory(BookTextProcessing::class)
			->create(['book_id' => $book->id, 'convert_new_lines_to_paragraphs' => true]);

		Artisan::call('book:text_waited_processing', ['latest_id' => $processing->id]);

		$processing->refresh();
		$section->refresh();

		$this->assertTrue($processing->isCompleted());

		$this->assertEquals('<p>текст</p><p>текст</p><p>текст</p>', $section->getContent());
	}

	public function testAddASpaceAfterTheFirstHyphenInTheParagraphOption()
	{
		$book = factory(Book::class)
			->states('with_section')
			->create();
		$book->forbid_to_change = true;
		$book->save();

		$section = $book->sections()->first();
		$section->content = '<p>-текст текст</p>';
		$section->save();
		$section->refresh();

		$processing = factory(BookTextProcessing::class)
			->create(['book_id' => $book->id, 'add_a_space_after_the_first_hyphen_in_the_paragraph' => true]);

		Artisan::call('book:text_waited_processing', ['latest_id' => $processing->id]);

		$processing->refresh();
		$section->refresh();

		$this->assertTrue($processing->isCompleted());

		$this->assertEquals('<p>- текст текст</p>', $section->getContent());
	}

	public function testAddASpaceAfterTheFirstHyphenInTheParagraph()
	{
		$command = new BookTextWaitedProcessingCommand();

		$content = '<p>-текст текст</p>';

		$section = new Section;
		$section->content = $content;

		$command->addASpaceAfterTheFirstHyphenInTheParagraph($section);

		$this->assertEquals('<p>- текст текст</p>', $section->saveXML());

		$section->content = '<p> -текст</p>';

		$command->addASpaceAfterTheFirstHyphenInTheParagraph($section);

		$this->assertEquals('<p>- текст</p>', $section->saveXML());

		$section->content = '<p> —текст</p>';

		$command->addASpaceAfterTheFirstHyphenInTheParagraph($section);

		$this->assertEquals('<p>— текст</p>', $section->saveXML());

		$section->content = '<p>-текст, - текст.</p>';

		$command->addASpaceAfterTheFirstHyphenInTheParagraph($section);

		$this->assertEquals('<p>- текст, - текст.</p>', $section->saveXML());

		$section->content = '<p>-т-е-к-с-т, - текст.</p>';

		$command->addASpaceAfterTheFirstHyphenInTheParagraph($section);

		$this->assertEquals('<p>- т-е-к-с-т, - текст.</p>', $section->saveXML());
	}

	public function testRemoveItalicOption()
	{
		$book = factory(Book::class)
			->states('with_section')
			->create();
		$book->forbid_to_change = true;
		$book->save();

		$section = $book->sections()->first();
		$section->content = '<p>текст <strong><em><strong>текст</strong></em></strong> <s>текст</s></p>';
		$section->save();
		$section->refresh();

		$processing = factory(BookTextProcessing::class)
			->create(['book_id' => $book->id, 'remove_italics' => true]);

		Artisan::call('book:text_waited_processing', ['latest_id' => $processing->id]);

		$processing->refresh();
		$section->refresh();

		$this->assertTrue($processing->isCompleted());

		$this->assertEquals('<p>текст <strong><strong>текст</strong></strong> <s>текст</s></p>', $section->getContent());
	}

	public function testRemoveSpacesBeforePunctuationMarksOption()
	{
		$book = factory(Book::class)
			->states('with_section')
			->create();
		$book->forbid_to_change = true;
		$book->save();

		$section = $book->sections()->first();
		$section->content = '<p>Текст , текст : текст ; текст ... текст ? текст ! текст . текст текст .</p>';
		$section->save();
		$section->refresh();

		$processing = factory(BookTextProcessing::class)
			->create(['book_id' => $book->id, 'remove_spaces_before_punctuations_marks' => true]);

		Artisan::call('book:text_waited_processing', ['latest_id' => $processing->id]);

		$processing->refresh();
		$section->refresh();

		$this->assertTrue($processing->isCompleted());

		$this->assertEquals('<p>Текст, текст: текст; текст... текст? текст! текст. текст текст.</p>', $section->getContent());
	}

	public function testAddSpacesAfterPunctuationsMarksOption()
	{
		$book = factory(Book::class)
			->states('with_section')
			->create();
		$book->forbid_to_change = true;
		$book->save();

		$section = $book->sections()->first();
		$section->content = '<p>И.вот.такого,рада!проблемы?бывают.</p>';
		$section->save();
		$section->refresh();

		$processing = factory(BookTextProcessing::class)
			->create(['book_id' => $book->id, 'add_spaces_after_punctuations_marks' => true]);

		Artisan::call('book:text_waited_processing', ['latest_id' => $processing->id]);

		$processing->refresh();
		$section->refresh();

		$this->assertTrue($processing->isCompleted());

		$this->assertEquals('<p>И. вот. такого, рада! проблемы? бывают.</p>', $section->getContent());
	}

	public function testMergeParagraphsIfThereIsNoDotAtTheEndOption()
	{
		$book = factory(Book::class)
			->states('with_section')
			->create();
		$book->forbid_to_change = true;
		$book->save();

		$section = $book->sections()->first();
		$section->content = '<p>текст</p><p>текст</p><p>текст.</p>';
		$section->save();
		$section->refresh();

		$processing = factory(BookTextProcessing::class)
			->create(['book_id' => $book->id, 'merge_paragraphs_if_there_is_no_dot_at_the_end' => true]);

		Artisan::call('book:text_waited_processing', ['latest_id' => $processing->id]);

		$processing->refresh();
		$section->refresh();

		$this->assertTrue($processing->isCompleted());

		$this->assertEquals('<p>текст текст текст.</p>', $section->getContent());
	}

	public function testRemoveItalicTag()
	{
		$command = new BookTextWaitedProcessingCommand();

		$content = '<p>текст <strong><em><strong>текст</strong></em></strong> <s>текст</s></p>';

		$this->assertEquals('<p>текст <strong><strong>текст</strong></strong> <s>текст</s></p>', $command->removeItalicTag($content));
	}

	public function testRemoveSpacesBeforePunctuationMarks()
	{
		$command = new BookTextWaitedProcessingCommand();

		$content = '<p>Вот , такого : рода    ; бывают    ... проблемы    ?  текста ! Хоть . глаз выколи .</p>';

		$section = new Section;
		$section->content = $command->removeSpacesBeforePunctuationMarks($content);

		$this->assertEquals('<p>Вот, такого: рода; бывают... проблемы? текста! Хоть. глаз выколи.</p>', $section->saveXML());

		$content = '<p>Текст 3 .14 текст 3 ,5 р. </p>';

		$section = new Section;
		$section->content = $command->removeSpacesBeforePunctuationMarks($content);

		$this->assertEquals('<p>Текст 3.14 текст 3,5 р.</p>', $section->saveXML());

		$content = '<p>Текст- текст</p>';

		$section = new Section;
		$section->content = $command->removeSpacesBeforePunctuationMarks($content);

		$this->assertEquals('<p>Текст- текст</p>', $section->saveXML());

		$content = '<p><span style="  text-align : left; color : #FFF ; ">текст</span></p>';

		$section = new Section;
		$section->content = $command->removeSpacesBeforePunctuationMarks($content);

		$this->assertEquals('<p><span style="text-align:left;color:#FFF;">текст</span></p>', $section->saveXML());
	}

	public function testAddSpacesAfterPunctuationsMarks()
	{
		$command = new BookTextWaitedProcessingCommand();

		$content = '<p>Вот,такого:рода;бывают...проблемы?текста!Хоть.глаз выколи.</p>';

		$section = new Section;
		$section->content = $command->addSpacesAfterPunctuationsMarks($content);

		$this->assertEquals('<p>Вот, такого: рода; бывают... проблемы? текста! Хоть. глаз выколи.</p>', $section->saveXML());

		$content = '<p>Текст 3.14 текст 3,5 р.</p>';

		$section = new Section;
		$section->content = $command->removeSpacesBeforePunctuationMarks($content);

		$this->assertEquals('<p>Текст 3.14 текст 3,5 р.</p>', $section->saveXML());

		$content = '<p>Текст-текст</p>';

		$section = new Section;
		$section->content = $command->removeSpacesBeforePunctuationMarks($content);

		$this->assertEquals('<p>Текст-текст</p>', $section->saveXML());

		$content = '<p><span style="text-align:left;color:#FFF;">текст</span></p>';

		$section = new Section;
		$section->content = $command->removeSpacesBeforePunctuationMarks($content);

		$this->assertEquals('<p><span style="text-align:left;color:#FFF;">текст</span></p>', $section->saveXML());

		$content = '<p>Текст,текст,текст.текст.текст.текст:текст:текст:текст</p>';

		$section = new Section;
		$section->content = $command->addSpacesAfterPunctuationsMarks($content);

		$this->assertEquals('<p>Текст, текст, текст. текст. текст. текст: текст: текст: текст</p>', $section->saveXML());

		$content = '<p>Текст...текст....текст....текст....текст....</p>';
		$section = new Section;
		$section->content = $command->addSpacesAfterPunctuationsMarks($content);
		$this->assertEquals('<p>Текст... текст.... текст.... текст.... текст....</p>', $section->saveXML());

		$content = '<p>Текст.…текст…текст….</p>';
		$section = new Section;
		$section->content = $command->addSpacesAfterPunctuationsMarks($content);
		$this->assertEquals('<p>Текст.… текст… текст….</p>', $section->saveXML());
	}

	public function testMergeParagraphsIfThereIsNoDotAtTheEnd()
	{
		$command = new BookTextWaitedProcessingCommand();

		$content = '<p>Текст текст</p> <p>текст текст. </p> <p>Текст текст. </p>';

		$section = new Section;
		$section->content = $command->mergeParagraphsIfThereIsNoDotAtTheEnd($content);

		$this->assertEquals('<p>Текст текст текст текст.</p><p>Текст текст.</p>', $section->saveXML());

		$content = '<p>Текст текст</p> <p>текст текст? </p> <p>Текст текст. </p>';

		$section = new Section;
		$section->content = $command->mergeParagraphsIfThereIsNoDotAtTheEnd($content);

		$this->assertEquals('<p>Текст текст текст текст?</p><p>Текст текст.</p>', $section->saveXML());

		$content = '<p>Текст текст, —</p> <p>текст текст. </p> <p>Текст текст! </p>';

		$section = new Section;
		$section->content = $command->mergeParagraphsIfThereIsNoDotAtTheEnd($content);

		$this->assertEquals('<p>Текст текст, — текст текст.</p><p>Текст текст!</p>', $section->saveXML());

		$content = '<p>Текст текст!</p> <p>Текст текст: </p> <p>текст текст! </p>';

		$section = new Section;
		$section->content = $command->mergeParagraphsIfThereIsNoDotAtTheEnd($content);

		$this->assertEquals('<p>Текст текст!</p><p>Текст текст: текст текст!</p>', $section->saveXML());

		$content = '<p>Текст</p><p>текст</p><p>текст!</p>';

		$section = new Section;
		$section->content = $command->mergeParagraphsIfThereIsNoDotAtTheEnd($content);

		$this->assertEquals('<p>Текст текст текст!</p>', $section->saveXML());
	}

	public function testTidyChapterTitle()
	{
		$command = new BookTextWaitedProcessingCommand();

		$command->isMustUpperCaseFixParam = true;

		$this->assertEquals('Глава восьмая. Название главы', $command->tidyChapterTitle('ГЛАВА ВОСЬМАЯ. НАЗВАНИЕ ГЛАВЫ'));

		$this->assertEquals('Глава 1', $command->tidyChapterTitle('ГЛАВА 1'));
		$this->assertEquals('Глава 45. Текст', $command->tidyChapterTitle('ГЛАВА 45. Текст'));
		$this->assertEquals('Глава 45. Текст', $command->tidyChapterTitle('ГЛАВА   45.   Текст'));
		$this->assertEquals('Глава 45. Текст...', $command->tidyChapterTitle('ГЛАВА   45.   Текст...'));
		$this->assertEquals('Глава 45... Текст', $command->tidyChapterTitle('ГЛАВА   45...   Текст'));
		$this->assertEquals('Глава 1', $command->tidyChapterTitle('   ГЛАВА 1'));
		$this->assertEquals('Глава 4', $command->tidyChapterTitle('глава  4'));
		$this->assertEquals('Глава 1', $command->tidyChapterTitle('Глава 1.'));
		$this->assertEquals('Глава 1', $command->tidyChapterTitle('глава 1.'));
		$this->assertEquals('Глава 1', $command->tidyChapterTitle('ГЛАВА 1.'));
		$this->assertEquals('Глава 14', $command->tidyChapterTitle('14 Глава.'));
		$this->assertEquals('Глава 18', $command->tidyChapterTitle('18 глава.'));
		$this->assertEquals('Глава 35', $command->tidyChapterTitle('35 ГЛАВА.'));
		$this->assertEquals('Глава 35', $command->tidyChapterTitle('35 ГЛАВА...'));
		$this->assertEquals('Глава 50', $command->tidyChapterTitle('50 ГЛАВА...'));
		$this->assertEquals('50 Глава 6...', $command->tidyChapterTitle('50 ГЛава 6...'));
		$this->assertEquals('Глава 1. Название.', $command->tidyChapterTitle('Глава 1. Название.'));
		$this->assertEquals('Глава 1... Название...', $command->tidyChapterTitle('Глава 1... Название...'));
		$this->assertEquals('Глава первая', $command->tidyChapterTitle('ГЛАВА ПЕРВАЯ'));
		$this->assertEquals('Глава восьмая', $command->tidyChapterTitle('ГЛАВА ВОСЬМАЯ'));
		$this->assertEquals('Текст', $command->tidyChapterTitle('ТЕКСТ'));
	}

	public function testGetUpperLettersCount()
	{
		$command = new BookTextWaitedProcessingCommand();

		$this->assertEquals(7, $command->getUpperLettersCount('глава  (234) ВОСЬМАЯ + @#$*&!@;'));
		$this->assertEquals(0, $command->getUpperLettersCount('   '));
	}

	public function testGetLowerLettersCount()
	{
		$command = new BookTextWaitedProcessingCommand();

		$this->assertEquals(5, $command->getLowerLettersCount('глава  (234) ВОСЬМАЯ + @#$*&!@;'));
		$this->assertEquals(0, $command->getLowerLettersCount('   '));
	}

	public function testGetUpperCaseLettersPercent()
	{
		$command = new BookTextWaitedProcessingCommand();

		$this->assertEquals(0, $command->getUpperCaseLettersPercent('(234)восьмая+;'));
		$this->assertEquals(40, $command->getUpperCaseLettersPercent('(234)тексттЕКСТ+;'));
		$this->assertEquals(50, $command->getUpperCaseLettersPercent('(234)текстТЕКСТ+;'));
		$this->assertEquals(60, $command->getUpperCaseLettersPercent('(234)тексТТЕКСТ+;'));
		$this->assertEquals(100, $command->getUpperCaseLettersPercent('(234)ВОСЬМАЯ+;'));
		$this->assertEquals(0, $command->getUpperCaseLettersPercent('(234)+;'));
	}

	public function testTidyChapterTitleCommand()
	{
		$book = factory(Book::class)
			->states('with_section')
			->create();
		$book->forbid_to_change = true;
		$book->save();

		$section = $book->sections()->first();
		$section->title = '3 ГЛАВА';
		$section->save();

		$section2 = factory(Section::class)->states('chapter')->create([
			'book_id' => $book->id,
			'title' => 'ГЛАВА ВТОРАЯ'
		]);

		$section3 = factory(Section::class)->states('chapter')->create([
			'book_id' => $book->id,
			'title' => 'ГЛАВА ТРЕТЬЯ'
		]);

		$section4 = factory(Section::class)->states('chapter')->create([
			'book_id' => $book->id,
			'title' => 'Глава ЧЕТВЕРТАЯ'
		]);

		$section5 = factory(Section::class)->states('chapter')->create([
			'book_id' => $book->id,
			'title' => 'Глава ПЯТАЯ'
		]);

		$processing = factory(BookTextProcessing::class)
			->create(['book_id' => $book->id, 'tidy_chapter_names' => true]);

		Artisan::call('book:text_waited_processing', ['latest_id' => $processing->id]);

		$processing->refresh();
		$section->refresh();
		$section2->refresh();
		$section3->refresh();
		$section4->refresh();
		$section5->refresh();

		$this->assertTrue($processing->isCompleted());

		$this->assertEquals('Глава 3', $section->title);
		$this->assertEquals('Глава вторая', $section2->title);
		$this->assertEquals('Глава третья', $section3->title);
		$this->assertEquals('Глава четвертая', $section4->title);
		$this->assertEquals('Глава пятая', $section5->title);
	}

	public function testRemoveExtraSpacesBeforeTheTextInTheParagraph()
	{
		$command = new BookTextWaitedProcessingCommand();

		$this->assertEquals(' &nbsp; &nbsp; &nbsp; ', $command->removeExtraSpacesBeforeTheTextInTheParagraph('  &nbsp; &nbsp; &nbsp; '));
		$this->assertEquals(' ', $command->removeExtraSpacesBeforeTheTextInTheParagraph('⠀⠀⠀⠀'));
	}

	public function testRemoveEmptyParagraphs()
	{
		$command = new BookTextWaitedProcessingCommand();

		$section = factory(Section::class)->states('chapter')->create();

		$section->content = '<p>текст</p><p> </p><div class="u-empty-line"></div><p>текст</p>';

		$section = $command->removeEmptyParagraphs($section);

		$this->assertEquals('<p>текст</p><p>текст</p>', $section->saveXML());

		$section->content = '<p>текст</p><p><img src="http://example.com/image.png" /></p><div class="u-empty-line"><img src="http://example.com/image.png" /></div><p>текст</p>';

		$section = $command->removeEmptyParagraphs($section);

		$this->assertEquals('<p>текст</p><p><img src="http://example.com/image.png" alt="image.png"/></p><div class="u-empty-line"><img src="http://example.com/image.png" alt="image.png"/></div><p>текст</p>',
			$section->saveXML());

		$section->content = '<p>текст</p><p><div class="u-empty-line"><img src="http://example.com/image.png" /></div></p><p>текст</p>';

		$section = $command->removeEmptyParagraphs($section);

		$this->assertEquals('<p>текст</p><div class="u-empty-line"><img src="http://example.com/image.png" alt="image.png"/></div><p>текст</p>',
			$section->saveXML());
	}

	public function testRemoveEmptyParagraphsOption()
	{
		$book = factory(Book::class)
			->states('with_section')
			->create();
		$book->forbid_to_change = true;
		$book->save();

		$section = factory(Section::class)->states('chapter')->create([
			'book_id' => $book->id,
			'content' => '<p>текст</p><p> </p><div class="u-empty-line"></div><p>текст</p>'
		]);

		$processing = factory(BookTextProcessing::class)
			->create(['book_id' => $book->id, 'remove_empty_paragraphs' => true]);

		Artisan::call('book:text_waited_processing', ['latest_id' => $processing->id]);

		$processing->refresh();
		$section->refresh();

		$this->assertTrue($processing->isCompleted());

		$this->assertEquals('<p>текст</p><p>текст</p>', $section->getContent());
	}

}
