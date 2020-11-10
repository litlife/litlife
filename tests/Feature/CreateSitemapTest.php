<?php

namespace Tests\Feature;

use App\Author;
use App\Blog;
use App\Book;
use App\Comment;
use App\Console\Commands\SitemapCreate;
use App\Forum;
use App\Genre;
use App\Keyword;
use App\Section;
use App\Sequence;
use App\Topic;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Litlife\Sitemap\Sitemap;
use Litlife\Sitemap\SitemapIndex;
use Litlife\Url\Url;
use Tests\TestCase;

class CreateSitemapTest extends TestCase
{
	private $storage;
	private $sitemapDirname;

	public function testSitemapRedirect()
	{
		$book = Book::factory()->create([
				'created_at' => now()->addMinute()
			]);

		Artisan::call('sitemap:create', ['create_new' => true, '--handle' => 'books', 'later_than_date' => now(), 'sendToSearchEngine' => false]);

		$this->assertTrue(Storage::disk($this->storage)->exists($this->sitemapDirname . '/sitemap.xml'));

		$redirect = Storage::disk($this->storage)->url($this->sitemapDirname . '/sitemap.xml');

		$this->get('/sitemap.xml')
			->assertRedirect($redirect);
	}

	public function testSitemapNotFound()
	{
		$this->assertFalse(Storage::disk($this->storage)->exists($this->sitemapDirname . '/sitemap.xml'));

		$redirect = Storage::disk($this->storage)->url($this->sitemapDirname . '/sitemap.xml');

		$this->get('/sitemap.xml')
			->assertNotFound();
	}

	public function testCreateSitemapPart()
	{
		$url = 'https://test.url';
		$lastmod = Carbon::create(2000, 01, 01, 01, 01, 01)->toW3cString();
		$changefreq = 'hourly';
		$priority = '1.0';

		$makeSitemap = new SitemapCreate();
		$makeSitemap->createSitemapIndex();
		$makeSitemap->createSitemap();
		$makeSitemap->addUrl($url, $lastmod, $changefreq, $priority);
		$makeSitemap->addUrl($url, $lastmod, $changefreq, $priority);
		$makeSitemap->saveCurrentSitemap();

		$location = $makeSitemap->getSitemapIndex()->getLastSitemap()['location'];

		$this->assertNotNull($location);

		$this->assertTrue(Storage::disk($this->storage)->exists($this->sitemapDirname . '/' . Url::fromString($location)->getBasename()));

		$xml = <<<EOF
<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">
  <url>
    <loc>https://test.url</loc>
    <lastmod>2000-01-01T01:01:01+00:00</lastmod>
    <changefreq>hourly</changefreq>
    <priority>1.0</priority>
  </url>
  <url>
    <loc>https://test.url</loc>
    <lastmod>2000-01-01T01:01:01+00:00</lastmod>
    <changefreq>hourly</changefreq>
    <priority>1.0</priority>
  </url>
</urlset>

EOF;

		$this->assertEquals($xml, Storage::disk($this->storage)->get($this->sitemapDirname . '/' . Url::fromString($location)->getBasename()));
		$this->assertEquals('274', $makeSitemap->getCurrentSitemap()->getSize());
		$this->assertEquals('2', $makeSitemap->getCurrentSitemap()->getUrlCount());

		$makeSitemap->saveSitemapIndexToFile();
	}

	public function testCheckCurrentSitemapSizeGetMaxSitemapPartLength()
	{
		$url = 'https://test.url';
		$lastmod = Carbon::create(2000, 01, 01, 01, 01, 01)->toW3cString();
		$changefreq = 'hourly';
		$priority = '1.0';

		$makeSitemap = new SitemapCreate();
		$makeSitemap->createSitemapIndex();
		$makeSitemap->createSitemap();

		$makeSitemap->getCurrentSitemap()->setMaxSize(1000);
		$makeSitemap->getCurrentSitemap()->setSize(999);

		$this->assertEquals(1000, $makeSitemap->getCurrentSitemap()->getMaxSize());
		$this->assertEquals(999, $makeSitemap->getCurrentSitemap()->getSize());
		//$this->assertEquals(1, $makeSitemap->getCurrentSitemapPartNumber());
		$this->assertEquals(0, $makeSitemap->getCurrentSitemap()->getUrlCount());

		$makeSitemap->addUrl($url, $lastmod, $changefreq, $priority);

		$makeSitemap->getCurrentSitemap()->setMaxSize(1000);
		$makeSitemap->getCurrentSitemap()->setSize(0);

		$this->assertEquals(0, $makeSitemap->getCurrentSitemap()->getSize());
		$this->assertEquals(0, $makeSitemap->getCurrentSitemap()->getUrlCount());

		$makeSitemap->addUrl($url, $lastmod, $changefreq, $priority);

		$this->assertEquals(137, $makeSitemap->getCurrentSitemap()->getSize());
		$this->assertEquals(1, $makeSitemap->getCurrentSitemap()->getUrlCount());

		$makeSitemap->addUrl($url, $lastmod, $changefreq, $priority);

		$this->assertEquals(274, $makeSitemap->getCurrentSitemap()->getSize());
		$this->assertEquals(2, $makeSitemap->getCurrentSitemap()->getUrlCount());

		$makeSitemap->saveSitemapIndexToFile();

		$this->assertEquals(2, $makeSitemap->getSitemapIndex()->getSitemapsCount());
	}

	public function testCheckCurrentSitemapSizeGetMaxSitemapPartURLCount()
	{
		$url = 'https://test.url';
		$lastmod = Carbon::create(2000, 01, 01, 01, 01, 01)->toW3cString();
		$changefreq = 'hourly';
		$priority = '1.0';

		$makeSitemap = new SitemapCreate();
		$makeSitemap->createSitemapIndex();
		$makeSitemap->createSitemap();

		$makeSitemap->getCurrentSitemap()->setMaxUrlCount(50);
		$makeSitemap->getCurrentSitemap()->setUrlCount(49);

		$this->assertEquals(50, $makeSitemap->getCurrentSitemap()->getMaxUrlCount());

		$this->assertEquals(49, $makeSitemap->getCurrentSitemap()->getUrlCount());

		$makeSitemap->addUrl($url, $lastmod, $changefreq, $priority);

		$this->assertEquals(0, $makeSitemap->getCurrentSitemap()->getUrlCount());

		$makeSitemap->addUrl($url, $lastmod, $changefreq, $priority);

		$this->assertEquals(1, $makeSitemap->getCurrentSitemap()->getUrlCount());
	}

	public function testBookComments()
	{
		$book = Book::factory()->create(['created_at' => now()->addMinute()]);

		$comments = factory(Comment::class, 12)
			->create(['commentable_type' => 'book', 'commentable_id' => $book->id]);

		config(['litlife.comments_on_page_count' => 3]);

		Artisan::call('sitemap:create', ['create_new' => true, '--handle' => 'books', 'later_than_date' => now(), 'sendToSearchEngine' => false]);

		$sitemap = $this->getSitemap();

		$url = $sitemap->getWhereLocation(route('books.show', ['book' => $book, 'page' => 2]));
		$this->assertNotNull($url);
		$this->assertEquals('weekly', $url['changefreq']);
		$this->assertEquals('0.6', $url['priority']);

		$url = $sitemap->getWhereLocation(route('books.show', ['book' => $book, 'page' => 3]));
		$this->assertNotNull($url);
		$this->assertEquals('weekly', $url['changefreq']);
		$this->assertEquals('0.6', $url['priority']);
	}

	public function getSitemap(): Sitemap
	{
		$content = Storage::disk($this->storage)
			->get($this->sitemapDirname . '/sitemap.xml');

		$index = new SitemapIndex();
		$index->open($content);

		$basename = Url::fromString($index->getLastSitemap()['location'])
			->getBasename();

		$content = Storage::disk($this->storage)
			->get($this->sitemapDirname . '/' . $basename);

		$sitemap = new Sitemap();
		$sitemap->open($content);

		return $sitemap;
	}

	public function testBookPagesNewFormat()
	{
		$section = Section::factory()->create();

		$book = $section->book;
		$book->statusAccepted();
		$book->created_at = now()->addMinute();
		$book->save();
		$book->refresh();

		Artisan::call('sitemap:create', ['create_new' => true, '--handle' => 'books', 'later_than_date' => now(), 'sendToSearchEngine' => false]);

		$sitemap = $this->getSitemap();

		$url = $sitemap->getWhereLocation(route('books.sections.index', ['book' => $book]));
		$this->assertNotNull($url);

		$url = $sitemap->getWhereLocation(route('books.sections.show', ['book' => $book, 'section' => $section->inner_id]));
		$this->assertNotNull($url);

		$url = $sitemap->getWhereLocation(route('books.sections.show', ['book' => $book, 'section' => $section->inner_id, 'page' => 2]));
		$this->assertNotNull($url);
	}

	public function testBookPagesOldFormat()
	{
		$book = Book::factory()->create([
				'created_at' => now()->addMinute()
			]);

		$book->online_read_new_format = false;
		$book->page_count = 3;
		$book->save();

		Artisan::call('sitemap:create', ['create_new' => true, '--handle' => 'books', 'later_than_date' => now(), 'sendToSearchEngine' => false]);

		$sitemap = $this->getSitemap();

		$url = $sitemap->getWhereLocation(route('books.old.page', ['book' => $book]));
		$this->assertNotNull($url);

		$url = $sitemap->getWhereLocation(route('books.old.page', ['book' => $book, 'page' => 2]));
		$this->assertNotNull($url);

		$url = $sitemap->getWhereLocation(route('books.old.page', ['book' => $book, 'page' => 3]));
		$this->assertNotNull($url);
	}

	public function testAuthor()
	{
		$author = Author::factory()->create([
				'created_at' => now()->addMinute()
			]);

		Artisan::call('sitemap:create', ['create_new' => true, '--handle' => 'authors', 'later_than_date' => now(), 'sendToSearchEngine' => false]);

		$sitemap = $this->getSitemap();

		$url = $sitemap->getWhereLocation(route('authors.show', ['author' => $author]));

		$this->assertNotNull($url);
		$this->assertEquals('weekly', $url['changefreq']);
		$this->assertEquals('0.6', $url['priority']);
	}

	public function testSequence()
	{
		$sequence = Sequence::factory()->create([
				'created_at' => now()->addMinute()
			]);

		Artisan::call('sitemap:create', ['create_new' => true, '--handle' => 'sequences', 'later_than_date' => now(), 'sendToSearchEngine' => false]);

		$sitemap = $this->getSitemap();

		$url = $sitemap->getWhereLocation(route('sequences.show', ['sequence' => $sequence]));

		$this->assertNotNull($url);
		$this->assertEquals('weekly', $url['changefreq']);
		$this->assertEquals('0.6', $url['priority']);
	}

	public function testUserWall()
	{
		$user = User::factory()->create([
				'created_at' => now()->addMinute()
			]);

		$blogs = factory(Blog::class, 21)
			->create(['blog_user_id' => $user->id]);

		Artisan::call('sitemap:create', ['create_new' => true, '--handle' => 'users', 'later_than_date' => now(), 'sendToSearchEngine' => false]);

		$sitemap = $this->getSitemap();

		$url = $sitemap->getWhereLocation(route('profile', ['user' => $user]));

		$this->assertNotNull($url);
		$this->assertEquals('weekly', $url['changefreq']);
		$this->assertEquals('0.6', $url['priority']);
		/*
				$url = $sitemap->getWhereLocation(route('profile', ['user' => $user, 'page' => 2]));

				$this->assertNotNull($url);
				$this->assertEquals('weekly', $url['changefreq']);
				$this->assertEquals('0.6', $url['priority']);

				$url = $sitemap->getWhereLocation(route('profile', ['user' => $user, 'page' => 3]));

				$this->assertNotNull($url);
				$this->assertEquals('weekly', $url['changefreq']);
				$this->assertEquals('0.6', $url['priority']);
				*/
	}

	public function testForum()
	{
		$forum = Forum::factory()->create([
				'created_at' => now()->addMinute()
			]);

		Artisan::call('sitemap:create', ['create_new' => true, '--handle' => 'forums', 'later_than_date' => now(), 'sendToSearchEngine' => false]);

		$sitemap = $this->getSitemap();

		$url = $sitemap->getWhereLocation(route('forums.show', ['forum' => $forum]));

		$this->assertNotNull($url);
		$this->assertEquals('weekly', $url['changefreq']);
		$this->assertEquals('0.6', $url['priority']);
	}

	public function testTopics()
	{
		$topic = Topic::factory()->create([
				'created_at' => now()->addMinute()
			]);

		Artisan::call('sitemap:create', ['create_new' => true, '--handle' => 'topics', 'later_than_date' => now(), 'sendToSearchEngine' => false]);

		$sitemap = $this->getSitemap();

		$url = $sitemap->getWhereLocation(route('topics.show', $topic));

		$this->assertNotNull($url);
		$this->assertEquals('daily', $url['changefreq']);
		$this->assertEquals('0.4', $url['priority']);
	}

	public function testGenre()
	{
		$genre = Genre::factory()->create();

		Artisan::call('sitemap:create', ['create_new' => true, '--handle' => 'genres', 'sendToSearchEngine' => false]);

		$sitemap = $this->getSitemap();

		$url = $sitemap->getWhereLocation(route('genres'));

		$this->assertNotNull($url);
		$this->assertEquals('weekly', $url['changefreq']);
		$this->assertEquals('0.8', $url['priority']);

		$url = $sitemap->getWhereLocation(route('genres.show', $genre->getIdWithSlug()));

		$this->assertNotNull($url);
	}

	public function testKeywordBooks()
	{
		$keyword = Keyword::factory()->create();

		Artisan::call('sitemap:create', ['create_new' => true, '--handle' => 'keywords', 'sendToSearchEngine' => false]);

		$sitemap = $this->getSitemap();

		$url = $sitemap->getWhereLocation(route('books', ['kw' => $keyword->text]));

		$this->assertNotNull($url);
		$this->assertEquals('weekly', $url['changefreq']);
		$this->assertEquals('0.5', $url['priority']);
	}

	public function testHome()
	{
		Artisan::call('sitemap:create', ['create_new' => true, '--handle' => 'home', 'sendToSearchEngine' => false]);

		$sitemap = $this->getSitemap();

		$url = $sitemap->getWhereLocation(route('home'));
		$this->assertNotNull($url);
	}

	public function testPopularBooks()
	{
		Artisan::call('sitemap:create', ['create_new' => true, '--handle' => 'popular_books', 'sendToSearchEngine' => false]);

		$sitemap = $this->getSitemap();

		$url = $sitemap->getWhereLocation(route('home.popular_books', ['period' => 'day']));
		$this->assertNotNull($url);

		$url = $sitemap->getWhereLocation(route('home.popular_books', ['period' => 'month']));
		$this->assertNotNull($url);

		$url = $sitemap->getWhereLocation(route('home.popular_books', ['period' => 'week']));
		$this->assertNotNull($url);

		$url = $sitemap->getWhereLocation(route('home.popular_books', ['period' => 'year']));
		$this->assertNotNull($url);
	}

	public function testGenerateNewName()
	{
		$makeSitemap = new SitemapCreate();

		$this->assertEquals($this->sitemapDirname . '/sitemap_' . Carbon::now()->format('Ymd_His') . '.xml', $makeSitemap->generateNewName());
	}

	public function testArtisanNewBook()
	{
		$book = factory(Book::class)
			->states('accepted', 'with_section')
			->create([
				'created_at' => now()->addMinute()
			]);

		Artisan::call('sitemap:create', ['create_new' => true, '--handle' => 'books', 'later_than_date' => now(), 'sendToSearchEngine' => false]);

		$sitemap = $this->getSitemap();

		$url = $sitemap->getWhereLocation(route('books.show', $book));

		$this->assertNotNull($url);
		$this->assertEquals('weekly', $url['changefreq']);
		$this->assertEquals('0.6', $url['priority']);

		$url = $sitemap->getWhereLocation(route('books.sections.index', $book));

		$this->assertNotNull($url);
		$this->assertEquals('weekly', $url['changefreq']);
		$this->assertEquals('0.6', $url['priority']);
	}

	public function testCreateNewTrue()
	{
		$later_than_date = now();

		$book = factory(Book::class)
			->states('accepted')
			->create([
				'created_at' => now()->addMinute()
			]);

		Artisan::call('sitemap:create', ['create_new' => true, '--handle' => 'books', 'later_than_date' => $later_than_date, 'sendToSearchEngine' => false]);

		$content = Storage::disk($this->storage)
			->get($this->sitemapDirname . '/sitemap.xml');

		$index = new SitemapIndex();
		$index->open($content);

		$basename = Url::fromString($index->getLastSitemap()['location'])
			->getBasename();

		$this->assertTrue(Storage::disk($this->storage)->exists($this->sitemapDirname . '/' . $basename));

		$book = factory(Book::class)
			->states('accepted')
			->create([
				'created_at' => now()->addMinute()
			]);

		Carbon::setTestNow(now()->addDay());

		Artisan::call('sitemap:create', ['create_new' => true, '--handle' => 'books', 'later_than_date' => $later_than_date, 'sendToSearchEngine' => false]);

		$content = Storage::disk($this->storage)
			->get($this->sitemapDirname . '/sitemap.xml');

		$index = new SitemapIndex();
		$index->open($content);

		$basename2 = Url::fromString($index->getLastSitemap()['location'])
			->getBasename();

		$this->assertTrue(Storage::disk($this->storage)->exists($this->sitemapDirname . '/' . $basename2));
		$this->assertFalse(Storage::disk($this->storage)->exists($this->sitemapDirname . '/' . $basename));
	}

	public function testCreateNewFalse()
	{
		$book = factory(Book::class)
			->states('accepted')
			->create([
				'created_at' => now()->addMinute()
			]);

		Artisan::call('sitemap:create', ['create_new' => true, '--handle' => 'books', 'later_than_date' => now(), 'sendToSearchEngine' => false]);

		$content = Storage::disk($this->storage)
			->get($this->sitemapDirname . '/sitemap.xml');

		$index = new SitemapIndex();
		$index->open($content);

		$basename = Url::fromString($index->getLastSitemap()['location'])
			->getBasename();

		$this->assertTrue(Storage::disk($this->storage)->exists($this->sitemapDirname . '/' . $basename));

		$book = factory(Book::class)
			->states('accepted')
			->create([
				'created_at' => now()->addMinute()
			]);

		Artisan::call('sitemap:create', ['create_new' => false, '--handle' => 'books', 'later_than_date' => now(), 'sendToSearchEngine' => false]);

		$content = Storage::disk($this->storage)
			->get($this->sitemapDirname . '/sitemap.xml');

		$index = new SitemapIndex();
		$index->open($content);

		$basename2 = Url::fromString($index->getLastSitemap()['location'])
			->getBasename();

		$this->assertTrue(Storage::disk($this->storage)->exists($this->sitemapDirname . '/' . $basename2));
		$this->assertTrue(Storage::disk($this->storage)->exists($this->sitemapDirname . '/' . $basename));
		$this->assertEquals($this->sitemapDirname . '/' . $basename, $this->sitemapDirname . '/' . $basename2);
	}

	protected function setUp(): void
	{
		parent::setUp();

		$this->sitemapDirname = config('litlife.sitemap_dirname');
		$this->storage = config('litlife.sitemap_storage');

		Storage::fake($this->storage);
	}
}
