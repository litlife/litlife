<?php

namespace Tests\Feature\Book;

use App\Author;
use App\Book;
use App\Console\Commands\BookFillDBFromSource;
use App\Library\AddFb2File;
use App\Sequence;
use App\User;
use Exception;
use Illuminate\Support\Facades\Storage;
use Litlife\Fb2\Fb2;
use Tests\TestCase;

class BookAddFb2Test extends TestCase
{
	/**
	 * A basic test example.
	 *
	 * @return void
	 */
	public function setUp(): void
	{
		parent::setUp();
	}

	public function testAddFb2File()
	{
		Storage::fake(config('filesystems.default'));

		Sequence::any()->whereName('Title')->delete();

		foreach (Author::any()->similaritySearch('FirstName MiddleName LastName')->get() as $author)
			$author->delete();

		foreach (Author::any()->similaritySearch('FirstName2 MiddleName2 LastName2')->get() as $author)
			$author->delete();

		$user = factory(User::class)
			->create();

		$book = factory(Book::class)->create([
			'create_user_id' => $user->id,
			'is_si' => false,
			'is_lp' => false,
			'age' => 0
		]);
		$book->statusPrivate();
		$book->save();

		$command = new BookFillDBFromSource();
		$command->setExtension('fb2');
		$command->setBook($book);
		$command->setStream(fopen(__DIR__ . '/Books/test.fb2', 'r'));
		$command->addFromFile();

		$book->refresh();

		$this->assertNotNull($book->cover);
		$this->assertEquals('image.png', $book->cover->name);

		$this->assertEquals('Title', $book->title);
		$this->assertEquals(1, $book->genres()->count());

		$this->assertEquals('{' . implode(',',
				$book->genres()->get()->pluck('id')->toArray()) . '}', $book->genres_helper);

		$this->assertEquals(2, $book->writers()->any()->count());
		$this->assertEquals(1, $book->translators()->any()->count());
		$this->assertEquals(1, $book->sequences()->any()->count());

		$author = $book->writers()->any()->get()->get(0);

		$this->assertTrue($user->is($author->create_user));

		$this->assertEquals(0, $author->pivot->order);
		$this->assertEquals(1, $author->books_count);

		$author = $book->writers()->any()->get()->get(1);
		$this->assertEquals(1, $author->pivot->order);
		$this->assertEquals(1, $author->books_count);

		$sequence = $book->sequences()->any()->first();

		$this->assertEquals('Title', $sequence->name);
		$this->assertEquals(1, $sequence->pivot->number);
		$this->assertEquals(1, $sequence->book_count);

		$translator = $book->translators()->any()->first();
		$this->assertEquals(0, $translator->pivot->order);

		$this->assertEquals('Publisher', $book->pi_pub);
		$this->assertEquals('City', $book->pi_city);
		$this->assertEquals('2000', $book->pi_year);
		$this->assertEquals('1-11-111111', $book->pi_isbn);

		$this->assertEquals('image.png', $book->attachments()->first()->name);
		$this->assertEquals(1, $book->attachments()->count());
		$this->assertEquals(11, $book->sections()->count());
		$this->assertEquals(3, $book->sections()->where('type', 'note')->count());

		$first_section = $book->sections()->where('type', 'section')->oldestWithId()->first();

		$this->assertNotEmpty($first_section);
		$this->assertStringContainsString('<img', $first_section->getContent());
		$this->assertStringContainsString('width="275" height="382" alt="image.png"/>', $first_section->getContent());

		$this->assertEquals(
			'<p><a data-type="note" data-section-id="8" href="' . route('books.notes.show', ['book' => $book->id, 'note' => '8', 'page' => '1']) . '#u-note_1' . '">note1text</a></p>',
			$book->sections()->findInnerIdOrFail(3)->getContentHandeled()
		);

		$this->assertTrue($book->sections()->findInnerIdOrFail(1)->isRoot());
		$this->assertTrue($book->sections()->findInnerIdOrFail(2)->isRoot());
		$this->assertTrue($book->sections()->findInnerIdOrFail(3)->isRoot());
		$this->assertTrue($book->sections()->findInnerIdOrFail(4)->isRoot());

		$this->assertTrue($book->sections()->findInnerIdOrFail(5)->isRoot());

		$this->assertFalse($book->sections()->findInnerIdOrFail(6)->isRoot());
		$this->assertTrue($book->sections()->findInnerIdOrFail(6)->isChildOf($book->sections()->findInnerIdOrFail(5)));

		$this->assertFalse($book->sections()->findInnerIdOrFail(7)->isRoot());
		$this->assertTrue($book->sections()->findInnerIdOrFail(7)->isChildOf($book->sections()->findInnerIdOrFail(6)));
	}

	public function testAddOrReplaceIds()
	{
		$addFb2File = new AddFb2File();
		$addFb2File->setFile(__DIR__ . '/Books/test.fb2');

		foreach ($addFb2File->fb2->getBodies() as $body) {

			foreach ($body->childs('section') as $number => $section) {

				$this->assertEquals('section_' . ($number + 1), $section->getNode()->getAttribute('id'));
			}
		}

		foreach ($addFb2File->fb2->getBodiesNotes() as $body) {

			foreach ($body->childs('section') as $number => $section) {

				$this->assertEquals('note_' . ($number + 1), $section->getNode()->getAttribute('id'));
			}
		}

		$xml = <<<EOF
<annotation>
    <p>Annotation <a l:href="#note_3">note5</a></p>
   </annotation>
EOF;

		$this->assertEquals($xml,
			$addFb2File->fb2->description()->getFirstChild('title-info')->getFirstChild('annotation')->getXml());
	}

	public function testAddFb2WithoutDescription()
	{
		Storage::fake(config('filesystems.default'));

		$book = factory(Book::class)->create([
			'create_user_id' => 50000,
			'is_si' => false,
			'is_lp' => false,
			'age' => 0,
			'page_count' => 0
		]);

		$addFb2File = new AddFb2File();
		$addFb2File->setBook($book);
		$addFb2File->setFile(__DIR__ . '/Books/test_without_description.fb2');
		$addFb2File->init();

		$this->assertEmpty($book->cover()->first());
		$this->assertEquals('image.png', $book->attachments()->first()->name);
		$this->assertEquals(1, $book->attachments()->count());
		$this->assertEquals(10, $book->sections()->count());
		$this->assertEquals(3, $book->sections()->where('type', 'note')->count());

		$this->assertEquals(5, $book->fresh()->page_count);
	}

	public function testAddFb2WithoutPublishInfo()
	{
		Storage::fake(config('filesystems.default'));

		$book = factory(Book::class)->create([
			'create_user_id' => 50000,
			'is_si' => false,
			'is_lp' => false,
			'age' => 0,
			'pi_pub' => '',
			'pi_city' => '',
			'pi_year' => null,
			'pi_isbn' => '',
		]);

		$addFb2File = new AddFb2File();
		$addFb2File->setBook($book);
		$addFb2File->setFile(__DIR__ . '/Books/test_without_publish_info.fb2');
		$addFb2File->init();

		$this->assertEquals('image.png', $book->attachments()->first()->name);
		$this->assertEquals(3, $book->sections()->where('type', 'note')->count());
		$this->assertEquals(1, $book->attachments()->count());
		$this->assertEquals(11, $book->sections()->count());

		$this->assertEmpty($book->pi_pub);
		$this->assertEmpty($book->pi_city);
		$this->assertEmpty($book->pi_year);
		$this->assertEmpty($book->pi_isbn);
	}

	public function testAddFb2ManyBodies()
	{
		Storage::fake(config('filesystems.default'));

		$book = factory(Book::class)->create([
			'create_user_id' => 50000,
			'is_si' => false,
			'is_lp' => false,
			'age' => 0
		]);

		$addFb2File = new AddFb2File();
		$addFb2File->setBook($book);
		$addFb2File->setFile(__DIR__ . '/Books/test_many_bodies.fb2');
		$addFb2File->init();

		$book->refresh();

		$sections = $book->sections()->orderBy('id', 'asc')->get()->toTree();

		// dd($sections);

		$this->assertEquals(__('section.untitled'), $sections[0]->title);
		$this->assertEquals(__('section.untitled'), $sections[0]->children[0]->title);

		$this->assertEquals('body2 title', $sections[1]->title);
		$this->assertEquals(__('section.untitled'), $sections[1]->children[0]->title);

		$this->assertEquals('body3 title', $sections[2]->title);
		$this->assertEquals(__('section.untitled'), $sections[2]->children[0]->title);

		$this->assertEquals('body4 title', $sections[3]->title);
		$this->assertEquals(__('section.untitled'), $sections[3]->children[0]->title);
	}

	public function testWrongFb2()
	{
		$addFb2File = new AddFb2File();

		try {
			$addFb2File->open(__DIR__ . '/Books/test.epub');
		} catch (Exception $exception) {

		}

		$this->assertEquals('Root element not found', $exception->getMessage());
	}

	public function testCyrylicBinaryNames()
	{
		$book = factory(Book::class)->create();

		$addFb2File = new AddFb2File();
		$addFb2File->setBook($book);
		$addFb2File->setFile(__DIR__ . '/Books/cyrylic_binary_names.fb2');
		$addFb2File->init();

		$book->refresh();

		$section = $book->sections()->where('type', 'section')->first();

		$this->assertEquals(1, $book->attachments()->count());
		$this->assertNotNull($book->attachments()->first());
		$this->assertEquals('nazvanie.png', $book->attachments()->first()->name);
		$this->assertEquals('Текст', $section->title);
		$this->assertEquals('<div class="u-empty-line"></div><p><img src="' . $book->attachments()->first()->url . '" width="340" height="332" alt="nazvanie.png"/></p><div class="u-empty-line"></div>',
			$section->getContent());
	}

	public function testWindows1251()
	{
		$book = factory(Book::class)->create();

		$addFb2File = new AddFb2File();
		$addFb2File->setBook($book);
		$addFb2File->setFile(__DIR__ . '/Books/windows1251_encoding.fb2');
		$addFb2File->init();

		$book->refresh();

		$section = $book->sections()->where('type', 'section')->first();

		$this->assertEquals('Текст главы', $section->title);
		$this->assertEquals('<div class="u-empty-line"></div>', $section->getContent());
	}

	public function testXlinkXmlns()
	{
		$book = factory(Book::class)->create();

		$addFb2File = new AddFb2File();
		$addFb2File->setBook($book);
		$addFb2File->setFile(__DIR__ . '/Books/xlink_xmlns.fb2');
		$addFb2File->init();

		$book->refresh();

		$sections = $book->sections()->where('type', 'section')->defaultOrder()->get();

		$section = $sections->get(0);
		$section1 = $sections->get(1);

		$this->assertEquals('Текст главы сноска', $section->title);
		$this->assertEquals('<div class="u-empty-line"></div><p><img src="' . $book->attachments()->first()->url . '" width="340" height="332" alt="nazvanie.png"/></p>',
			$section->getContent());

		$this->assertEquals('Текст главы', $section1->title);
		$this->assertEquals('', $section1->getContent());
	}

	public function testCheckIfImageAlreadyAttachedToBook()
	{
		Storage::fake(config('filesystems.default'));

		$book = factory(Book::class)->create();

		$addFb2File = new AddFb2File();
		$addFb2File->setBook($book);
		$addFb2File->setFile(__DIR__ . '/Books/test.fb2');
		$addFb2File->init();

		$attachment = $book->attachments()->first();

		$this->assertNotNull($attachment);
		$this->assertEquals(1, $book->attachments()->count());

		$addFb2File = new AddFb2File();
		$addFb2File->setBook($book);
		$addFb2File->setFile(__DIR__ . '/Books/test.fb2');
		$addFb2File->init();

		$this->assertEquals(1, $book->attachments()->count());
	}

	public function testWithoutTitleInfo()
	{
		$book = factory(Book::class)
			->create();

		$addFb2File = new AddFb2File();
		$addFb2File->setBook($book);
		$addFb2File->setFile(__DIR__ . '/Books/without_title.fb2');
		$addFb2File->init();

		$section = $book->sections()->where('type', 'section')->defaultOrder()->first();

		$this->assertEquals('Текст книги', $section->title);
		$this->assertEquals('<div class="u-empty-line"></div>', $section->getContent());
	}

	public function testHandleContentUnexpectedTags()
	{
		$html = '<p><div>test</div></p>';

		$addFb2File = new AddFb2File();

		$this->assertEquals('<p></p><div>test</div>', $addFb2File->handleContent($html));
	}

	public function testWithoutAnnotation()
	{
		$book = factory(Book::class)
			->create();

		$addFb2File = new AddFb2File();
		$addFb2File->setBook($book);
		$addFb2File->setFile(__DIR__ . '/Books/without_annotation.fb2');
		$addFb2File->init();

		$book->refresh();

		$this->assertNull($book->annotation);
	}

	public function testWithEmptyPublishInfo()
	{
		$book = factory(Book::class)
			->create();

		$addFb2File = new AddFb2File();
		$addFb2File->setBook($book);
		$addFb2File->setFile(__DIR__ . '/Books/test_with_publisher_without_city_year.fb2');
		$addFb2File->init();

		$book->refresh();

		$this->assertEquals('Publisher', $book->pi_pub);
		$this->assertEquals('', $book->pi_city);
		$this->assertEquals('', $book->pi_year);
	}

	public function testWithoutContentType()
	{
		$book = factory(Book::class)
			->create();

		$addFb2File = new AddFb2File();
		$addFb2File->setBook($book);
		$addFb2File->setFile(__DIR__ . '/Books/test_binary_without_content_type.fb2');
		$addFb2File->init();

		$book->refresh();

		$attachement = $book->attachments()->first();

		$this->assertEquals(1, $book->attachments()->count());
		$this->assertEquals('image/png', $attachement->content_type);
	}

	public function testUpperCaseExtensionBinaryNames()
	{
		$book = factory(Book::class)
			->create();

		$addFb2File = new AddFb2File();
		$addFb2File->setBook($book);
		$addFb2File->setFile(__DIR__ . '/Books/upper_case_extension_binary_names.fb2');
		$addFb2File->init();

		$book->refresh();

		$attachement = $book->attachments()->first();

		$this->assertEquals(1, $book->attachments()->count());
		$this->assertEquals('test.png', $attachement->name);
		$this->assertEquals('test.PNG', $attachement->parameters['fb_name']);
		$this->assertEquals('image/png', $attachement->content_type);
		$this->assertRegExp('/(.*)test\.png$/iu', $book->attachments()->first()->url);

		$this->assertNull($book->annotation);

		$this->assertEquals(1, $book->sections()->chapter()->count());

		$section = $book->sections()->chapter()->first();

		$this->assertEquals('Текст', $section->title);

		$this->assertEquals('<div class="u-empty-line"></div><p><img src="' . $book->attachments()->first()->url . '" width="340" height="332" alt="test.png"/></p><div class="u-empty-line"></div>',
			$section->getContent());
	}

	public function testWrongExtensionBinaryName()
	{
		$book = factory(Book::class)
			->create();

		$addFb2File = new AddFb2File();
		$addFb2File->setBook($book);
		$addFb2File->setFile(__DIR__ . '/Books/wrong_extension_binary_names.fb2');
		$addFb2File->init();

		$book->refresh();

		$attachement = $book->attachments()->first();

		$this->assertEquals(1, $book->attachments()->count());
		$this->assertEquals('test.png', $attachement->name);
		$this->assertEquals('test.PNG_0', $attachement->parameters['fb_name']);
		$this->assertEquals('image/png', $attachement->content_type);
		$this->assertRegExp('/(.*)test\.png$/iu', $book->attachments()->first()->url);

		$this->assertNull($book->annotation);

		$this->assertEquals(1, $book->sections()->chapter()->count());

		$section = $book->sections()->chapter()->first();

		$this->assertEquals('Текст', $section->title);

		$this->assertEquals('<div class="u-empty-line"></div><p><img src="' . $book->attachments()->first()->url . '" width="340" height="332" alt="test.png"/></p><div class="u-empty-line"></div>',
			$section->getContent());
	}

	public function testRemoteUrl()
	{
		$book = factory(Book::class)
			->create();

		$addFb2File = new AddFb2File();
		$addFb2File->setBook($book);
		$addFb2File->setFile(__DIR__ . '/Books/test_with_remote_url.fb2');
		$addFb2File->init();

		$book->refresh();

		$section = $book->sections()->chapter()->first();

		$this->assertEquals('<p>текст <a href="/away?url=https%3A%2F%2Fexample.com%2Ftest%2F%3Fte.s%3At%26t~e%26st">текст</a> текст</p>',
			$section->getContentHandeled());

		$note = $book->sections()->notes()->first();

		$this->assertEquals('<p>текст <a href="/away?url=http%3A%2F%2Fexample.com%2Ftest">ссылка в сноске</a> текст</p>',
			$note->getContentHandeled());

		$annotation = $book->annotation()->first();

		$this->assertEquals('<p>Annotation <a href="/away?url=http%3A%2F%2Fexample.com%2Ftest">ссылка</a></p>',
			$annotation->getContentHandeled());
	}

	public function testWrongBinary()
	{
		$book = factory(Book::class)
			->create();

		$addFb2File = new AddFb2File();
		$addFb2File->setBook($book);
		$addFb2File->setFile(__DIR__ . '/Books/test_wrong_binary.fb2');
		$addFb2File->init();

		$book->refresh();

		$section = $book->sections()->chapter()->first();

		$this->assertEquals('<p>section 1 text</p><div class="u-empty-line"></div><p></p><div class="u-empty-line"></div>',
			$section->getContentHandeled());

		$this->assertEquals(0, $book->attachments()->count());
	}

	public function testEmptyTags()
	{
		$book = factory(Book::class)
			->create();

		$addFb2File = new AddFb2File();
		$addFb2File->setBook($book);
		$addFb2File->setFile(__DIR__ . '/Books/test_empty_tags.fb2');
		$addFb2File->init();

		$book->refresh();

		$this->assertEquals(0, $book->authors()->count());
		$this->assertEquals(0, $book->genres()->count());
		$this->assertEquals(0, $book->sections()->count());

		$this->assertEquals('', $book->title);
		$this->assertEquals('', $book->pi_pub);
		$this->assertEquals('', $book->pi_city);
		$this->assertEquals('', $book->pi_year);
		$this->assertEquals('', $book->pi_isbn);
	}

	public function test()
	{
		$book = factory(Book::class)
			->create();

		$addFb2File = new AddFb2File();
		$addFb2File->setBook($book);
		$addFb2File->setFile(__DIR__ . '/Books/test_chapters_names.fb2');
		$addFb2File->init();

		$chapters = $book->sections()
			->chapter()
			->defaultOrder()
			->get();

		$this->assertEquals('Текст первой главы', $chapters->get(0)->title);
		$this->assertEquals('', $chapters->get(0)->getContent());

		$this->assertEquals('Текст второй', $chapters->get(1)->title);
		$this->assertEquals('<p><b>главы</b></p>', $chapters->get(1)->getContent());

		$this->assertEquals('Текст третьей главы', $chapters->get(2)->title);
		$this->assertEquals('', $chapters->get(2)->getContent());

		$this->assertEquals('Текст четвертой', $chapters->get(3)->title);
		$this->assertEquals('<p>главы</p>', $chapters->get(3)->getContent());
	}

	public function testSequenceSearch()
	{
		Storage::fake(config('filesystems.default'));

		$fb2 = new Fb2();
		$fb2->setFile(__DIR__ . '/Books/test.fb2');

		foreach ($fb2->description()->getFirstChild('title-info')->childs('sequences') as $sequence) {

			$this->assertEquals('Title', $sequence->getNode()->getAttribute('name'));
			$this->assertEquals('1', $sequence->getNode()->getAttribute('number'));
		}

		Sequence::where('name', 'ilike', 'Title')
			->delete();

		$book = factory(Book::class)->create([
			'create_user_id' => 50000,
			'is_si' => false,
			'is_lp' => false,
			'age' => 0
		]);

		$sequence = factory(Sequence::class)
			->create(['name' => 'New title']);

		$addFb2File = new AddFb2File();
		$addFb2File->setBook($book);
		$addFb2File->setFile(__DIR__ . '/Books/test.fb2');
		$addFb2File->init();

		$book->refresh();

		$this->assertEquals('Title', $book->sequences->first()->name);
	}
}
