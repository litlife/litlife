<?php

namespace Tests\Feature\Book;

use App\Book;
use App\Console\Commands\BookFillDBFromSource;
use App\Library\AddEpubFile;
use App\Library\CreateEpubFile;
use App\Section;
use Illuminate\Support\Facades\Storage;
use Litlife\Epub\Epub;
use Litlife\Epub\EpubDescription;
use Litlife\Epub\Image;
use Litlife\Url\Url;
use Tests\TestCase;

class BookAddEpubTest extends TestCase
{
	/**
	 * A basic test example.
	 *
	 * @return void
	 */
	public function testMakeFile()
	{
		$book = factory(Book::class)
			->create();

		$command = new BookFillDBFromSource();
		$command->setExtension('epub');
		$command->setBook($book);
		$command->setStream(fopen(__DIR__ . '/Books/test.epub', 'r'));
		$command->addFromFile();

		$book->refresh();

		$this->assertNotNull($book->cover);
		$this->assertEquals(2, $book->sections()->where('type', 'section')->count());
		$this->assertEquals(1, $book->sections()->where('type', 'annotation')->count());

		$createEpubFile = new CreateEpubFile();
		$createEpubFile->setBookid($book->id);
		$createEpubFile->init();

		$stream = tmpfile();
		fwrite($stream, $createEpubFile->getEpub()->outputAsString());
		$path = stream_get_meta_data($stream)['uri'];

		$epub = new EpubDescription();
		$epub->setFile($path);

		$this->assertFile($epub);

		$book2 = factory(Book::class)
			->create();

		$command = new BookFillDBFromSource();
		$command->setExtension('epub');
		$command->setBook($book);
		$command->setStream(fopen($path, 'r'));
		$command->addFromFile();

		$createEpubFile = new CreateEpubFile();
		$createEpubFile->setBookid($book->id);
		$createEpubFile->init();

		$stream = tmpfile();
		fwrite($stream, $createEpubFile->getEpub()->outputAsString());
		$path = stream_get_meta_data($stream)['uri'];

		$epub = new EpubDescription();
		$epub->setFile($path);

		$this->assertFile($epub);
	}

	private function assertFile(Epub $epub)
	{
		$this->assertContains('OEBPS/content.opf', $epub->getAllFilesList());
		$this->assertContains('META-INF/container.xml', $epub->getAllFilesList());
		$this->assertContains('OEBPS/toc.ncx', $epub->getAllFilesList());

		$this->assertNotNull($epub->ncx());

		$this->assertEquals(1, count($epub->getImages()));
		$this->assertEquals(2, count($epub->getSectionsList()));

		$this->assertEquals('u-section-1', $epub->getSectionByFilePath('OEBPS/Text/section_1.xhtml')->getBodyId());

		$this->assertEquals('[Title here]', $epub->getTitle());
		$this->assertEquals('Publisher', $epub->getPublisher());
		$this->assertEquals('City', $epub->getPublishCity());
		$this->assertEquals('2002', $epub->getPublishYear());
		$this->assertEquals('en', $epub->getLanguage());
		$this->assertEquals('Annotation', $epub->getAnnotation());
		$this->assertEquals('rightsholder', $epub->getRightsholder());
		$this->assertEquals('2001', $epub->getCreatedDate());
		$this->assertEquals('111-1-111-11111-1', $epub->getISBN());

		$this->assertEquals('test.png', $epub->opf()->getMetaDataContentByName('cover'));
		$this->assertInstanceOf(Image::class, $epub->getCover());
		$this->assertEquals('OEBPS/Images/test.png', $epub->getCover()->getPath());

		$genres = $epub->getGenres();

		$this->assertEquals('sci_anachem', $genres[0]);
		$this->assertEquals('music', $genres[1]);

		$authors = $epub->getAuthors();

		var_dump($authors = $epub->getAuthors());

		$this->assertEquals('Author First Name', $authors[0]);
		$this->assertEquals('Author2 First2 Name2', $authors[1]);

		$sequences = $epub->getSequences();

		$this->assertEquals('SequenceName', $sequences[0]['name']);
		$this->assertEquals('1', $sequences[0]['number']);

		$this->assertEquals('SequenceName2', $sequences[1]['name']);
		$this->assertFalse(isset($sequences[1]['number']));

		$translators = $epub->getTranslators();

		$this->assertEquals('Translator First Name', $translators[0]);

		$section = pos($epub->getSectionsList());

		$this->assertEquals('Первая глава', $section->getTitle());
		$this->assertEquals('<h1 class="title">Первая глава</h1><p>Porro hic libero <a href="../Text/section_2.xhtml#u-section-2">note</a> dolorem. Dolor <a id="u-anchor1">note</a> quia impedit et corrupti. Laborum quos sit facere ut at illum. Nobis accusantium libero <a href="../Text/section_2.xhtml#u-section-2">sit</a> eos. Sunt quia nulla quibusdam dolores. Mollitia dolorum quisquam voluptatum aperiam. Aut voluptatum accusantium alias voluptatem rerum quis illo et. Reiciendis ab minima aut suscipit. Mollitia velit eligendi quidem est. Facere rerum qui ut recusandae explicabo temporibus. Animi aut architecto eos rerum aut. Amet est explicabo minima nulla. Consequatur esse voluptatem vel voluptatem. Molestiae ad omnis magni amet. Aliquam voluptates odit dolorem praesentium nulla ullam. Totam consectetur cupiditate laborum sequi esse. Exercitationem velit dolores ut natus accusamus. Non nulla error voluptatum qui eum nam. Voluptate fuga facere odio autem maiores. <img alt="test" src="../Images/test.png" width="340" height="332"/></p>',
			$section->getBodyContent());

		foreach ($section->xpath()->query("//*[local-name()='a']") as $number => $node) {

			$url = (string)Url::fromString($node->getAttribute("href"))
				->getPathRelativelyToAnotherUrl($section->getPath());

			switch ($number) {
				case 0:
					$this->assertEquals('OEBPS/Text/section_2.xhtml#u-section-2', $url);
					break;
				case 2:
					$this->assertEquals('OEBPS/Text/section_2.xhtml#u-section-2', $url);
					break;
			}
		}

		foreach ($section->xpath()->query("//*[local-name()='img']") as $number => $node) {

			$url = (string)Url::fromString($node->getAttribute("src"))
				->getPathRelativelyToAnotherUrl($section->getPath());

			switch ($number) {
				case 0:
					$this->assertEquals('OEBPS/Images/test.png', $url);
					$this->assertEquals('340', $node->getAttribute('width'));
					$this->assertEquals('332', $node->getAttribute('height'));
					break;
			}
		}

		$section = $epub->getFileByPath('OEBPS/Text/section_2.xhtml');

		$this->assertEquals('Вторая глава', $section->getTitle());
		$this->assertEquals('<h1 class="title">Вторая глава</h1><p>Porro hic <a href="../Text/section_1.xhtml#u-anchor1">to anchor</a> beatae dolorem. Dolor impedit quia impedit et corrupti. Laborum quos sit facere ut at illum. Nobis accusantium libero sit eos. Sunt quia nulla quibusdam dolores. Mollitia dolorum quisquam voluptatum aperiam. Aut voluptatum accusantium alias voluptatem rerum quis illo et. Reiciendis ab minima aut suscipit. Mollitia velit eligendi quidem est. Facere rerum qui ut recusandae explicabo temporibus. Animi aut architecto eos rerum aut. Amet est explicabo minima nulla. Consequatur esse voluptatem vel voluptatem. Molestiae ad omnis magni amet. Aliquam voluptates odit dolorem praesentium nulla ullam. Totam consectetur cupiditate laborum sequi esse. Exercitationem velit dolores ut natus accusamus. Non nulla error voluptatum qui eum nam. Voluptate fuga facere odio autem maiores.</p>',
			$section->getBodyContent());

		foreach ($section->xpath()->query("//*[local-name()='a'][@href]") as $number => $node) {

			$url = (string)Url::fromString($node->getAttribute("href"))
				->getPathRelativelyToAnotherUrl($section->getPath());

			switch ($number) {
				case 0:
					$this->assertEquals('OEBPS/Text/section_1.xhtml#u-anchor1', $url);
					break;
			}
		}
	}

	public function testGetCyrylicFileNames()
	{
		$epub = new EpubDescription();
		$epub->setFile(__DIR__ . '/Books/test_cyrylic_names.epub');

		$section = $epub->getFileByPath('OEBPS/Text/Текст.xhtml');

		$this->assertEquals('<p>text</p><p><img alt="изображение" src="../Images/%D0%B8%D0%B7%D0%BE%D0%B1%D1%80%D0%B0%D0%B6%D0%B5%D0%BD%D0%B8%D0%B5.png"/><br/></p><p><a href="../Text/%D0%A2%D0%B5%D0%BA%D1%81%D1%822.xhtml">ссылка</a><br/></p>',
			$section->getBodyContent());

		$image = $epub->getFileByPath('OEBPS/Images/изображение.png');

		$this->assertEquals(340, $image->getWidth());
		$this->assertEquals(332, $image->getHeight());
	}

	public function testAddWithCyrylicFileNames()
	{
		Storage::fake(config('filesystems.default'));

		$book = factory(Book::class)
			->create();

		$addEpubFile = new AddEpubFile();
		$addEpubFile->setBook($book);
		$addEpubFile->setFile(__DIR__ . '/Books/test_cyrylic_names.epub');
		$addEpubFile->init();

		$text = $book->sections()->defaultOrder()->get()[0]->getContent();

		$this->assertEquals('<p>text</p><p><img alt="изображение" src="' . $book->attachments()->first()->url . '" width="340" height="332"/><br/></p><p><a href="#u-section-2">ссылка</a><br/></p>', $text);

		$cover = $book->cover()->first();

		$this->assertEquals('izobrazenie.png', $cover->name);
	}

	public function testNewLineToSpace()
	{
		$book = factory(Book::class)
			->create();

		$addEpubFile = new AddEpubFile();
		$addEpubFile->setBook($book);
		$addEpubFile->open(__DIR__ . '/Books/test.epub');

		$content = $addEpubFile->epub
			->getFileByPath('OEBPS/Text/Section0001.xhtml')
			->getBodyContent();

		$this->assertEquals('<p>Porro hic libero <a href="../Text/Section0002.xhtml#section-2">note</a> dolorem. Dolor <a id="anchor1">note</a> quia impedit et corrupti. Laborum quos sit facere ut at illum. Nobis accusantium libero <a href="../Text/Section0002.xhtml#section-2">sit</a> eos. Sunt quia nulla quibusdam dolores. Mollitia dolorum quisquam voluptatum aperiam. Aut voluptatum accusantium alias voluptatem rerum quis illo et. Reiciendis ab minima aut suscipit. Mollitia velit eligendi quidem est. Facere rerum qui ut recusandae explicabo temporibus. Animi aut architecto eos rerum aut. Amet est explicabo minima nulla. Consequatur esse voluptatem vel voluptatem. Molestiae ad omnis magni amet. Aliquam voluptates odit dolorem praesentium nulla ullam. Totam consectetur cupiditate laborum sequi esse. Exercitationem velit dolores ut natus accusamus. Non nulla error voluptatum qui eum nam. Voluptate fuga facere odio autem maiores. <img alt="test" src="../Images/test.png"/> </p>', $content);
	}

	public function testTitleDontPrependAfterAdd()
	{
		$title = $this->faker->realText(50);
		$content = $this->faker->realText(50);

		$section = factory(Section::class)
			->create([
				'title' => $title,
				'content' => $content
			]);

		$book = $section->book;

		$createEpubFile = new CreateEpubFile();
		$createEpubFile->setBookid($book->id);
		$createEpubFile->init();

		$stream = tmpfile();
		fwrite($stream, $createEpubFile->getEpub()->outputAsString());
		$path = stream_get_meta_data($stream)['uri'];

		$book2 = factory(Book::class)->create();

		$addEpubFile = new AddEpubFile();
		$addEpubFile->setBook($book);
		$addEpubFile->open($path);
		$addEpubFile->init();

		$book->refresh();

		$section = $book->sections()->where('type', 'section')->first();

		$this->assertEquals('<p>' . $content . '</p>',
			$section->getContent());
	}

	public function testUpperCaseImageName()
	{
		$book = factory(Book::class)
			->create();

		$addEpubFile = new AddEpubFile();
		$addEpubFile->setBook($book);
		$addEpubFile->open(__DIR__ . '/Books/upper_case_image_name.epub');
		$addEpubFile->init();

		$attachment = $book->attachments()->first();

		$this->assertEquals('test.png', $attachment->name);
		$this->assertEquals('image/png', $attachment->content_type);
		$this->assertEquals('OEBPS/Images/test.PNG', $attachment->parameters['epub_path']);

		$text = $book->sections()->defaultOrder()->get()[0]->getContent();

		$this->assertEquals('<p>text</p><p><img alt="изображение" src="' . $attachment->url . '" width="340" height="332"/><br/></p><p><a href="#u-section-2">ссылка</a><br/></p>', $text);

		$cover = $book->cover()->first();

		$this->assertEquals('test.png', $cover->name);
	}

	public function testWrongExtensionBinaryName()
	{
		$book = factory(Book::class)
			->create();

		$addEpubFile = new AddEpubFile();
		$addEpubFile->setBook($book);
		$addEpubFile->open(__DIR__ . '/Books/wrong_extensions_image_name.epub');
		$addEpubFile->init();

		$attachment = $book->attachments()->first();

		$this->assertEquals('test.png', $attachment->name);
		$this->assertEquals('image/png', $attachment->content_type);
		$this->assertEquals('OEBPS/Images/test.PNG_0', $attachment->parameters['epub_path']);

		$text = $book->sections()->defaultOrder()->get()[0]->getContent();

		$this->assertEquals('<p>text</p><p><img alt="изображение" src="' . $attachment->url . '" width="340" height="332"/><br/></p><p><a href="#u-section-2">ссылка</a><br/></p>', $text);

		$cover = $book->cover()->first();

		$this->assertEquals('test.png', $cover->name);
	}

	public function testRemoteUrl()
	{
		$book = factory(Book::class)
			->create();

		$addEpubFile = new AddEpubFile();
		$addEpubFile->setBook($book);
		$addEpubFile->open(__DIR__ . '/Books/test_remote_url.epub');
		$addEpubFile->init();

		$text = $book->sections()->get()[1]->getContent();

		$this->assertEquals('<p>текст <a href="/away?url=https%3A%2F%2Fexample.com%2Ftest%2F%3Fte.s%3At%26t~e%26st">текст</a> текст</p>', $text);
	}

	public function testDontAddLinearNo()
	{
		$book = factory(Book::class)
			->create();

		$addEpubFile = new AddEpubFile();
		$addEpubFile->setBook($book);
		$addEpubFile->open(__DIR__ . '/Books/test_with_linear_no.epub');
		$addEpubFile->init();

		$this->assertEquals(1, $book->sections()->chapter()->count());

		$chapters = $book->sections()->chapter()->get();

		$this->assertEquals('<p>текст первой главы</p>',
			$chapters[0]->getContent());
	}

	public function testTitleAnchor()
	{
		$book = factory(Book::class)
			->create();

		$addEpubFile = new AddEpubFile();
		$addEpubFile->setBook($book);
		$addEpubFile->open(__DIR__ . '/Books/test_header_anchor.epub');
		$addEpubFile->init();

		$chapters = $book->sections()->chapter()->defaultOrder()->get();

		$this->assertEquals('Глава 1', $chapters[0]->title);
		$this->assertEquals('<p>текст первой главы <a href="#u-header2">сноска</a></p>', $chapters[0]->getContent());
		$this->assertEquals('u-header1', $chapters[0]->getTitleId());

		$this->assertEquals('Глава 2', $chapters[1]->title);
		$this->assertEquals('<p>текст второй главы <a href="#u-header1">сноска</a></p>', $chapters[1]->getContent());
		$this->assertEquals('u-header2', $chapters[1]->getTitleId());
	}

	public function testRemoveEmptySectionWithoutImages()
	{
		$book = factory(Book::class)
			->create();

		$addEpubFile = new AddEpubFile();
		$addEpubFile->setBook($book);
		$addEpubFile->open(__DIR__ . '/Books/test_svg_image.epub');
		$addEpubFile->init();

		$chapters = $book->sections()
			->chapter()
			->defaultOrder()
			->get();

		$this->assertEquals(1, $chapters->count());

		$this->assertEquals('Вторая глава', $chapters[0]->title);
		$this->assertEquals('<p>test</p>', $chapters[0]->getContent());
	}

	public function testImage()
	{
		$book = factory(Book::class)
			->create();

		$addEpubFile = new AddEpubFile();
		$addEpubFile->setBook($book);
		$addEpubFile->open(__DIR__ . '/Books/test_image.epub');
		$addEpubFile->init();

		$chapters = $book->sections()
			->chapter()
			->defaultOrder()
			->get();

		$this->assertEquals(2, $chapters->count());

		$this->assertEquals('Первая глава', $chapters[0]->title);
		$this->assertStringContainsString('<img width="340" height="332"', $chapters[0]->getContent());

		$this->assertEquals('Вторая глава', $chapters[1]->title);
		$this->assertEquals('<p>test</p>', $chapters[1]->getContent());
	}
}
