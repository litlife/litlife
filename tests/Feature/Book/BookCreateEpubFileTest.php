<?php

namespace Tests\Feature\Book;

use App\Attachment;
use App\Author;
use App\Book;
use App\Library\CreateEpubFile;
use App\Section;
use App\Sequence;
use Carbon\Carbon;
use Litlife\Epub\Css;
use Litlife\Epub\Epub;
use Litlife\Epub\EpubDescription;
use Tests\TestCase;

class BookCreateEpubFileTest extends TestCase
{
	public function testSectionCreated()
	{
		$section = factory(Section::class)
			->create();

		$book = $section->book;

		$epub = $this->createEpubAndOpenFile($book);

		$this->assertEquals('<h1 class="title">' . $section->title . '</h1>' . $section->getContent(),
			$epub->getFileByPath('OEBPS/Text/section_1.xhtml')->getBodyContent());
	}

	private function createEpubAndOpenFile($book): EpubDescription
	{
		$createEpubFile = new CreateEpubFile();
		$createEpubFile->setBookid($book->id);
		$createEpubFile->init();

		$stream = tmpfile();
		fwrite($stream, $createEpubFile->getEpub()->outputAsString());
		$path = stream_get_meta_data($stream)['uri'];

		$epub = new EpubDescription();
		$epub->setFile($path);

		return $epub;
	}

	public function testAwayUrls()
	{
		$xhtml = '<p> <a href="https://example.com/path/%D0%AD%D0%BB%D0%B5%D0%BC%D0%B5%D0%BD%D1%82?query=%D0%AD%D0%BB%D0%B5%D0%BC%D0%B5%D0%BD%D1%82">https://example.com/path</a> </p>';

		$section = factory(Section::class)
			->create();
		$section->content = $xhtml;
		$section->save();

		$book = $section->book;

		$section->refresh();

		$epub = $this->createEpubAndOpenFile($book);

		$this->assertEquals('<h1 class="title">' . $section->title . '</h1>' . $xhtml, $epub->getFileByPath('OEBPS/Text/section_1.xhtml')->getBodyContent());
	}

	public function testImage()
	{
		$book = factory(Book::class)->create();

		$attachement = factory(Attachment::class)->create(['book_id' => $book->id]);

		$xhtml = '<p><img src="' . $attachement->url . '"/></p>';

		$section = factory(Section::class)->create(['book_id' => $book->id]);
		$section->content = $xhtml;
		$section->save();
		$section->refresh();

		$book = $section->book;

		$epub = $this->createEpubAndOpenFile($book);

		$this->assertEquals('<h1 class="title">' . $section->title . '</h1>' . '<p> <img src="../Images/test.jpeg" alt="test.jpeg"/> </p>',
			$epub->getFileByPath('OEBPS/Text/section_1.xhtml')->getBodyContent());
	}

	public function testNoteLink()
	{
		$xhtml = '<p> <a href="#u-section-1">test</a> </p>';

		$section = factory(Section::class)->create();
		$section->content = $xhtml;
		$section->save();
		$section->refresh();

		$book = $section->book;

		$epub = $this->createEpubAndOpenFile($book);

		$this->assertEquals('<h1 class="title">' . $section->title . '</h1>' . '<p> <a href="../Text/section_1.xhtml#u-section-1">test</a> </p>',
			$epub->getFileByPath('OEBPS/Text/section_1.xhtml')->getBodyContent());
	}

	public function testNoteLinkWithUrl()
	{
		$book = factory(Book::class)->create();

		$section = factory(Section::class)
			->create(['book_id' => $book->id]);

		$xhtml = '<p> <a href="' . route('books.sections.show', ['book' => $book->id, 'section' => $section->inner_id]) . '#u-section-2">test</a> </p>';

		$section2 = factory(Section::class)->create(['book_id' => $book->id]);
		$section2->content = $xhtml;
		$section2->save();
		$section2->refresh();

		$epub = $this->createEpubAndOpenFile($book);

		$this->assertEquals('<h1 class="title">' . $section2->title . '</h1>' . $xhtml,
			$epub->getFileByPath('OEBPS/Text/section_2.xhtml')->getBodyContent());
	}

	public function testExternalLinkWithHash()
	{
		$section = factory(Section::class)
			->create(['content' => '<p> <a href="http://example.com/test#u-section-1">test</a> </p>']);

		$book = $section->book;

		$epub = $this->createEpubAndOpenFile($book);

		$this->assertEquals('<h1 class="title">' . $section->title . '</h1>' . '<p> <a href="http://example.com/test#u-section-1">test</a> </p>',
			$epub->getFileByPath('OEBPS/Text/section_1.xhtml')->getBodyContent());
	}

	public function testWrongSymbols()
	{
		$string = 'test &><" test';

		$book = factory(Book::class)->create();
		$book->title = $string;
		$book->pi_pub = $string;
		$book->rightholder = $string;
		$book->pi_city = $string;
		$book->pi_isbn = $string;
		$book->save();

		$author = factory(Author::class)
			->create([
				'last_name' => $string,
				'first_name' => $string,
				'middle_name' => $string,
				'nickname' => $string,
			]);
		$book->writers()->sync([$author->id]);

		$sequence = factory(Sequence::class)->create(['name' => $string]);
		$book->sequences()->sync([$sequence->id]);

		$annotation = factory(Section::class)
			->states('annotation')
			->create(['book_id' => $book->id]);
		$annotation->content = $string;
		$annotation->save();

		$section = factory(Section::class)
			->create(['book_id' => $book->id, 'title' => $string, 'content' => $string]);
		$section->content = $string;
		$section->save();

		$book->refresh();

		$epub = $this->createEpubAndOpenFile($book);

		$this->assertEquals($string, $epub->getTitle());
		$this->assertEquals($string, $epub->getPublisher());
		$this->assertEquals($string, $epub->getPublishCity());
		$this->assertEquals($string, $epub->getRightsholder());
		$this->assertEquals('', $epub->getISBN());

		$author = $book->writers()->first();

		$this->assertEquals($string, $author->last_name);
		$this->assertEquals($string, $author->first_name);
		$this->assertEquals($string, $author->middle_name);
		$this->assertEquals($string, $author->nickname);

		$sequence = $book->sequences()->first();

		$this->assertEquals($string, $sequence->name);

		$annotation = $book->annotation;

		$this->assertEquals('<p>' . htmlspecialchars($string, ENT_NOQUOTES) . '</p>', $annotation->getContent());

		$section = $book->sections()
			->where('type', 'section')
			->first();

		$this->assertEquals('test &>', $section->title);
		$this->assertEquals('<p>' . htmlspecialchars($string, ENT_NOQUOTES) . '</p>', $section->getContent());
	}

	public function testAssertRightSection()
	{
		$book = factory(Book::class)->create();

		$section = factory(Section::class)->create([
			'book_id' => $book->id,
			'content' => '<p><a href="#u-test3">текст</a></p><p><a href="#u-test4">текст</a></p>'
		]);

		$section3 = factory(Section::class)->create([
			'book_id' => $book->id,
			'content' => '<p><span id="u-test3">текст</span></p>'
		]);

		$section4 = factory(Section::class)->create([
			'book_id' => $book->id,
			'content' => '<p><span id="u-test4">текст</span></p>'
		]);

		$epub = $this->createEpubAndOpenFile($book);

		$this->assertEquals('<h1 class="title">' . $section->title . '</h1>' . '<p> <a href="../Text/section_2.xhtml#u-test3">текст</a> </p><p> <a href="../Text/section_3.xhtml#u-test4">текст</a> </p>',
			$epub->getFileByPath('OEBPS/Text/section_1.xhtml')->getBodyContent());
	}

	public function testHasCss()
	{
		$book = factory(Book::class)->create();

		$epub = $this->createEpubAndOpenFile($book);

		$this->assertInstanceOf(Css::class, $epub->getFileByPath('OEBPS/Styles/main.css'));
	}

	public function testHasModificationDate()
	{
		$book = factory(Book::class)->create();

		$epub = $this->createEpubAndOpenFile($book);

		$this->assertEquals('<dc:date xmlns:opf="http://www.idpf.org/2007/opf" opf:event="modification">' . Carbon::now()->format('Y-m-d') . '</dc:date>',
			$epub->opf()->dom()->saveXML($epub->opf()->getDublinCoreByName('date')->item(1)));
	}

	public function testIfCreatorOfBookDeleted()
	{
		$book = factory(Book::class)->states('with_create_user')->create();
		$book->create_user->delete();

		$epub = $this->createEpubAndOpenFile($book);

		$this->assertInstanceOf(Epub::class, $epub);
	}

	public function testSectionPrependTitle()
	{
		$title = '< & > $ %' . $this->faker->realText(50);

		$section = factory(Section::class)
			->create(['title' => $title]);

		$book = $section->book;

		$epub = $this->createEpubAndOpenFile($book);

		$this->assertEquals('<h1 class="title">' . htmlspecialchars($title) . '</h1>' . $section->getContent(),
			$epub->getFileByPath('OEBPS/Text/section_1.xhtml')->getBodyContent());
	}

	public function testFtpUrlSchema()
	{
		$content = '<p><a href="/away?url=ftp%3A%2F%2Fftp">FTP://ftp</a>/test.pdf</p>';

		$section = factory(Section::class)
			->create(['content' => $content]);

		$book = $section->book;

		$epub = $this->createEpubAndOpenFile($book);

		$this->assertEquals('<h1 class="title">' . htmlspecialchars($section->title) . '</h1><p><a href="ftp://ftp">FTP://ftp</a>/test.pdf</p>',
			$epub->getFileByPath('OEBPS/Text/section_1.xhtml')->getBodyContent());
	}
}