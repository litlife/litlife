<?php

namespace Tests\Feature\Book;

use App\Book;
use App\Jobs\Book\UpdateBookSectionsCount;
use App\Library\BookSqlite;
use App\Library\Old\xsBookPath;
use ErrorException;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use PDO;
use Tests\TestCase;

class BookSqliteTest extends TestCase
{
	private $path;
	private $tmp_file;
	private $pdo;
	private $book;
	private $db_path;

	/**
	 * A basic feature test example.
	 *
	 * @return void
	 */
	public function setUp(): void
	{
		parent::setUp();

		//$this->tmp_file = tmpfile();

		$this->book = factory(Book::class)
			->create([
				'online_read_new_format' => false,
				'page_count' => 1
			]);

		$this->db_path = $this->book->getBookPath();

		mkdir(pathinfo($this->db_path, PATHINFO_DIRNAME), null, true);
		$l = fopen($this->db_path, 'w+');
		fclose($l);

		Config::set("database.connections.sqlite_memory", [
			'driver' => 'sqlite',
			'database' => $this->db_path,
			'prefix' => '',
		]);

		$this->pdo = DB::connection('sqlite_memory')->getPdo();

		Schema::connection("sqlite_memory")->create("binary", function (Blueprint $table) {
			$table->increments('br_id');
			$table->string('br_code')->nullable();
			$table->string('br_is_image');
			$table->string('br_name');
			$table->string('br_mime_type');
			$table->timestamp('br_edit_time');
			$table->string('br_param')->nullable();
			$table->string('br_content');
			$table->integer('br_file_size');
			$table->string('br_md5', 32);
		});

		Schema::connection("sqlite_memory")
			->create("pages", function (Blueprint $table) {
				$table->integer('id');
				$table->string('text');
			});

		Schema::connection("sqlite_memory")
			->create("other", function (Blueprint $table) {
				$table->string('name');
				$table->string('content');
			});
	}

	public function tearDown(): void
	{
		$this->disconnent();

		$this->pdo = null;

		if (file_exists($this->db_path))
			unlink($this->db_path);

		$storage = Storage::disk('old');

		foreach ($storage->directories() as $directory) {
			$this->directory($storage, $directory);
		}

		parent::tearDown();
	}

	public function directory($storage, $directory)
	{
		if (empty($storage->files($directory)) and empty($storage->directories($directory))) {
			try {
				$storage->deleteDirectory($directory);
			} catch (ErrorException $exception) {

			}
		} else {
			foreach ($storage->directories($directory) as $directory) {
				$this->directory($storage, $directory);
			}
		}
	}

	public function disconnent()
	{
		DB::disconnect('sqlite_memory');
	}

	public function testPageHttp()
	{
		$text = $this->faker->realText(100);

		$this->createPage(1, $text);

		$this->get(route('books.old.page', ['book' => $this->book, 'page' => 1]))
			->assertOk()
			->assertSeeText($text);
	}

	public function createPage($page, $text)
	{
		DB::connection('sqlite_memory')
			->table('pages')
			->insert([
				[
					'id' => $page,
					'text' => gzcompress($text)
				],
			]);

		$sqlite = new BookSqlite();
		$sqlite->setPdoConnection($this->pdo);

		//dd($sqlite->pagesCount());
	}

	public function testBinaryHttp()
	{
		$text = $this->faker->realText(100);

		$this->createImage();

		$response = $this->get(route('books.old.image', ['book' => $this->book, 'name' => 'test.jpeg']))
			->assertOk();

		$md5 = md5($response->getContent());

		$this->assertEquals('0d919b06e12b161f66b9830894b714db', $md5);
		$this->assertEquals($md5, md5(file_get_contents(__DIR__ . '/../images/test.jpeg')));
	}

	public function createImage($name = 'test.jpeg', $mime_type = 'image/jpeg', $path = null)
	{
		if (empty($path))
			$path = __DIR__ . '/../images/test.jpeg';

		$content = file_get_contents($path);

		$img = Image::make($content);

		$param = [
			'w' => $img->width(),
			'h' => $img->height()
		];

		DB::connection('sqlite_memory')
			->table('binary')
			->insert([
				[
					'br_is_image' => true,
					'br_code' => null,
					'br_name' => $name,
					'br_mime_type' => 'image/jpeg',
					'br_edit_time' => now(),
					'br_param' => serialize($param),
					'br_content' => $content,
					'br_file_size' => strlen($content),
					'br_md5' => md5($content)
				],
			]);
	}

	public function testBinaryInPageHttp()
	{
		$text = 'text <!--[litru_binary]test.jpeg[/litru_binary]--> text';

		$this->createPage(1, $text);
		$this->createImage();

		$response = $this->get(route('books.old.page', ['book' => $this->book, 'page' => 1]))
			->assertOk();

		$this->assertStringContainsString('text <img width="604" height="604" alt="' . $this->book->title . ' test.jpeg" src="' . route('books.old.image', ['book' => $this->book, 'name' => 'test.jpeg']) . '"> text',
			$response->getContent());
	}

	public function testPagesCount()
	{
		$text1 = $this->faker->realText(100);
		$this->createPage(1, $text1);

		$text2 = $this->faker->realText(100);
		$this->createPage(2, $text2);

		$text3 = $this->faker->realText(100);
		$this->createPage(3, $text3);

		$sqlite = new BookSqlite();
		$sqlite->setPdoConnection($this->pdo);

		$this->assertEquals(3, $sqlite->pagesCount());
		$this->assertEquals(3, $sqlite->pagesCount());

		$statement = $sqlite->getConnection()->prepare('SELECT "content" FROM other WHERE name = "pages_count" LIMIT 1');
		$statement->execute();

		$this->assertEquals(3, pos($statement->fetchAll(PDO::FETCH_COLUMN)));
	}

	public function testPageContent()
	{
		$text1 = $this->faker->realText(100);
		$this->createPage(1, $text1);

		$text2 = $this->faker->realText(100);
		$this->createPage(2, $text2);

		$text3 = $this->faker->realText(100);
		$this->createPage(3, $text3);

		$sqlite = new BookSqlite();
		$sqlite->setPdoConnection($this->pdo);

		$this->assertEquals(mb_strlen($text1), mb_strlen($sqlite->pageContent(1)));
		$this->assertEquals(mb_strlen($text2), mb_strlen($sqlite->pageContent(2)));
		$this->assertEquals(mb_strlen($text3), mb_strlen($sqlite->pageContent(3)));
	}

	public function testSectionsCount()
	{
		$this->createSectionsList();

		$sqlite = new BookSqlite();
		$sqlite->setPdoConnection($this->pdo);

		$this->assertEquals(2, $sqlite->sectionsCount());
	}

	public function createSectionsList()
	{
		$array = [
			[
				'title' => 'Заголовок 1',
				'page' => '1',
				'sn' => 'section_1',
				'level' => '1',
				'number' => '1',
				'ch' => [
					[
						'title' => 'Заголовок 2',
						'page' => '2',
						'sn' => 'section_2',
						'level' => '2',
						'number' => '2'
					]
				]
			]
		];

		DB::connection('sqlite_memory')
			->table('other')
			->insert([
				[
					'name' => 'titles',
					'content' => gzcompress(serialize($array))
				],
			]);

		DB::connection('sqlite_memory')
			->table('other')
			->insert([
				[
					'name' => 'section_titles_count',
					'content' => 2
				],
			]);
	}

	public function testSections()
	{
		$this->createSectionsList();

		$sqlite = new BookSqlite();
		$sqlite->setPdoConnection($this->pdo);

		$section1 = $sqlite->sections()[0];

		$this->assertEquals('Заголовок 1', $section1['title']);
		$this->assertEquals('1', $section1['page']);
		$this->assertEquals('section_1', $section1['sn']);
		$this->assertEquals('1', $section1['level']);
		$this->assertEquals('1', $section1['number']);

		$section2 = $sqlite->sections()[0]['ch'][0];

		$this->assertEquals('Заголовок 2', $section2['title']);
		$this->assertEquals('2', $section2['page']);
		$this->assertEquals('section_2', $section2['sn']);
		$this->assertEquals('2', $section2['level']);
		$this->assertEquals('2', $section2['number']);

		$this->get(route('books.sections.index', ['book' => $this->book]))
			->assertOk();
	}

	public function testBinaryList()
	{
		$this->createImage();

		$sqlite = new BookSqlite();
		$sqlite->setPdoConnection($this->pdo);

		$this->assertEquals('1', $sqlite->binaryList()[0]['br_id']);
	}

	public function testBinaryContentById()
	{
		$this->createImage();

		$sqlite = new BookSqlite();
		$sqlite->setPdoConnection($this->pdo);

		$content = $sqlite->binaryContentById(1)['br_content'];

		$this->assertEquals('99346', strlen($content));
		$this->assertEquals('0d919b06e12b161f66b9830894b714db', md5($content));
	}

	public function testBinaryContentByName()
	{
		$this->createImage();

		$sqlite = new BookSqlite();
		$sqlite->setPdoConnection($this->pdo);

		$content = $sqlite->binaryContentByName('test.jpeg')['br_content'];

		$this->assertEquals('99346', strlen($content));
		$this->assertEquals('0d919b06e12b161f66b9830894b714db', md5($content));
	}

	public function testDBNotFound()
	{
		$this->disconnent();

		$response = $this->get(route('books.old.page', ['book' => $this->book, 'page' => 1]))
			->assertNotFound();

		$response = $this->get(route('books.old.image', ['book' => $this->book, 'name' => 'test.jpeg']))
			->assertNotFound();
	}

	public function testCharactersCount()
	{
		$text1 = '123 !';
		$this->createPage(1, $text1);

		$text2 = '<strong>text text</strong>';
		$this->createPage(2, $text2);

		$text3 = '   <tag>привет как дела</tag>   ';
		$this->createPage(3, $text3);

		$sqlite = new BookSqlite();
		$sqlite->setPdoConnection($this->pdo);

		$this->assertEquals(25, $sqlite->getCharactersCount());
	}

	public function testCharactersCountIfNoPages()
	{
		$sqlite = new BookSqlite();
		$sqlite->setPdoConnection($this->pdo);

		$this->assertEquals(0, $sqlite->getCharactersCount());
	}

	public function testPath()
	{
		$path = xsBookPath::ReturnPathPart(10);

		$this->assertEquals('Book/0/0/10', $path);

		$path = xsBookPath::ReturnPathPart(12345);

		$this->assertEquals('Book/0/12000/12345', $path);

		$path = xsBookPath::ReturnPathPart(543215);

		$this->assertEquals('Book/0/543000/543215', $path);

		$path = xsBookPath::GetLocalPath(543215);

		$this->assertEquals(old_data_path() . '/Book/0/543000/543215', $path);
	}

	public function testBookDeleted404Error()
	{
		$text = $this->faker->realText(200);

		$this->createPage(1, $text);
		$this->createImage('test.jpeg', 'image/jpeg');

		$this->get(route('books.old.page', ['book' => $this->book, 'page' => 1]))
			->assertOk()
			->assertSeeText($text);

		$response = $this->get(route('books.old.image', ['book' => $this->book, 'name' => 'test.jpeg']))
			->assertOk();

		$this->book->delete();

		$this->get(route('books.old.page', ['book' => $this->book, 'page' => 1]))
			->assertNotFound()
			->assertDontSeeText($text);

		$response = $this->get(route('books.old.image', ['book' => $this->book, 'name' => 'test.jpeg']))
			->assertNotFound();

		$this->get(route('books.sections.index', ['book' => $this->book]))
			->assertOk();
	}

	public function testUpdateSectionsCount()
	{
		$this->createSectionsList();

		$sqlite = new BookSqlite();
		$sqlite->setPdoConnection($this->pdo);

		$section1 = $sqlite->sections()[0];

		$this->assertEquals('Заголовок 1', $section1['title']);
		$this->assertEquals('1', $section1['page']);
		$this->assertEquals('section_1', $section1['sn']);
		$this->assertEquals('1', $section1['level']);
		$this->assertEquals('1', $section1['number']);

		$section2 = $sqlite->sections()[0]['ch'][0];

		$this->assertEquals('Заголовок 2', $section2['title']);
		$this->assertEquals('2', $section2['page']);
		$this->assertEquals('section_2', $section2['sn']);
		$this->assertEquals('2', $section2['level']);
		$this->assertEquals('2', $section2['number']);

		UpdateBookSectionsCount::dispatch($this->book);

		$this->book->refresh();

		$this->assertEquals(2, $this->book->sections_count);
	}

	public function testUpdateSectionsIfDBNotFound()
	{
		$this->disconnent();

		UpdateBookSectionsCount::dispatch($this->book);

		$this->book->refresh();

		$this->assertEquals(0, $this->book->sections_count);
	}

	public function testEmptySn()
	{
		$array = [
			[
				'title' => 'Заголовок 1',
				'page' => '1',
				'level' => '1',
				'number' => '1',
				'ch' => [
					[
						'title' => 'Заголовок 2',
						'page' => '2',
						'sn' => 'section_2',
						'level' => '2',
						'number' => '2'
					]
				]
			]
		];

		DB::connection('sqlite_memory')
			->table('other')
			->insert([
				[
					'name' => 'titles',
					'content' => gzcompress(serialize($array))
				],
			]);

		$this->get(route('books.sections.index', ['book' => $this->book]))
			->assertOk()
			->assertSee('"' . route('books.old.page', ['book' => $this->book, 'page' => 1]) . '"', false)
			->assertSee('"' . route('books.old.page', ['book' => $this->book, 'page' => 2]) . '#section_2"', false);
	}
}


