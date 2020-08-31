<?php

namespace App\Console\Commands;

use App\Author;
use App\Book;
use App\Comment;
use App\Forum;
use App\Genre;
use App\Keyword;
use App\Sequence;
use App\Topic;
use App\User;
use Carbon\Carbon;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Litlife\Sitemap\Sitemap;
use Litlife\Sitemap\SitemapIndex;
use Litlife\Url\Url;

class SitemapCreate extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'sitemap:create 
                                 {create_new=true} 
                                 {sendToSearchEngine=true}
                                 {later_than_date?}
                                 {--handle=all}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Создать карту сайта';
	private $storage;
	private $sitemapDirname;
	private $sitemapIndex;
	private $currentSitemap;
	private $olderThanDate;
	private $createNew = true;
	private $currentSitemapName;

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();

		$this->sitemapDirname = config('litlife.sitemap_dirname');
		$this->storage = config('litlife.sitemap_storage');
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		$this->createNew = $this->argument('create_new');

		if (!empty($this->argument('later_than_date')))
			$this->olderThanDate = Carbon::parse($this->argument('later_than_date'));

		if ($this->createNew) {
			$this->clearDirectory();
			$this->createSitemapIndex();
			$this->createSitemap();

		} else {

			$content = Storage::disk($this->storage)
				->get($this->sitemapDirname . '/sitemap.xml');

			$this->sitemapIndex = new SitemapIndex();
			$this->sitemapIndex->open($content);

			$lastSitemap = $this->sitemapIndex->getLastSitemap();

			$basename = Url::fromString($lastSitemap['location'])
				->getBasename();

			$content = Storage::disk($this->storage)
				->get($this->sitemapDirname . '/' . $basename);

			$this->currentSitemap = new Sitemap();
			$this->currentSitemap->open($content);
			$this->currentSitemapName = $basename;
		}

		$handle = $this->option('handle');

		if ($handle == 'all') {
			$this->home();
			$this->popular_books();
			$this->genres();
			$this->authors();
			$this->sequences();
			$this->keywords();
			$this->books();
			$this->users();
			$this->forums();
			$this->topics();
		} else {
			$handleArray = explode(',', $handle);

			foreach ($handleArray as $name) {
				$this->{$name}();
			}
		}

		$this->saveSitemapIndexToFile();

		if ($this->argument('sendToSearchEngine'))
			$this->sendToSearchEngine();

		return true;
	}

	private function clearDirectory()
	{
		if (Storage::disk($this->storage)->exists($this->sitemapDirname)) {
			foreach (Storage::disk($this->storage)->allFiles($this->sitemapDirname) as $file) {
				if (!Storage::disk($this->storage)->delete($file)) {
					throw new Exception('Файл не удален ' . $file);
				}
			}

		} else {
			Storage::disk($this->storage)
				->makeDirectory($this->sitemapDirname);
		}
	}

	public function createSitemapIndex()
	{
		$this->sitemapIndex = new SitemapIndex();
	}

	public function createSitemap()
	{
		$this->currentSitemap = new Sitemap();
	}

	public function home()
	{
		$this->info('home');

		$this->addUrl(route('home'), now(), 'hourly', '1.0');
		$this->addUrl(route('home.latest_books'), now(), 'hourly', 0.8);
		$this->addUrl(route('home.latest_comments'), now(), 'hourly', 0.8);
		$this->addUrl(route('home.latest_posts'), now(), 'hourly', 0.8);
		$this->addUrl(route('home.latest_wall_posts'), now(), 'hourly', 0.8);
	}

	public function addUrl($location, $lastmod = null, $changefreq = 'weekly', $priority = '0.5')
	{
		$this->getCurrentSitemap()->addUrl($location, $lastmod, $changefreq, $priority);

		if ($this->getCurrentSitemap()->isCountOfURLsIsGreaterOrEqualsThanMax() or $this->getCurrentSitemap()->isSizeLargerOrEqualsThanMax()) {
			$this->saveCurrentSitemap();

			$this->currentSitemap = new Sitemap();
		}
	}

	public function getCurrentSitemap(): Sitemap
	{
		return $this->currentSitemap;
	}

	public function saveCurrentSitemap()
	{
		if ($this->createNew)
			$path = $this->generateNewName();
		else
			$path = $this->sitemapDirname . '/' . $this->currentSitemapName;

		Storage::disk($this->storage)
			->put(
				$path,
				$this->getCurrentSitemap()->getContent()
			);

		if (isset($this->output))
			$this->info("\n" . 'File ' . $path . ' created');

		$this->sitemapIndex->addSitemap(
			Storage::disk($this->storage)->url($path),
			now());
	}

	public function generateNewName()
	{
		$fileName = fileNameFormat('sitemap_' . Carbon::now()->format('Y-m-d H:i:s') . '.xml');

		return $this->sitemapDirname . '/' . $fileName;
	}

	public function popular_books()
	{
		$this->info('popular_books');

		$this->addUrl(route('home.popular_books', ['period' => 'day']), now(), 'daily', 0.8);
		$this->addUrl(route('home.popular_books', ['period' => 'week']), now(), 'daily', 0.8);
		$this->addUrl(route('home.popular_books', ['period' => 'month']), now(), 'daily', 0.8);
		$this->addUrl(route('home.popular_books', ['period' => 'quarter']), now(), 'daily', 0.8);
		$this->addUrl(route('home.popular_books', ['period' => 'year']), now(), 'daily', 0.8);
	}

	public function genres()
	{
		$this->info('genres');

		$this->addUrl(
			route('genres'), now(), 'weekly', 0.8
		);

		$genres = Genre::when(!empty($this->olderThanDate), function ($query) {
			$query->where('created_at', '>', $this->olderThanDate);
		})->get();

		foreach ($genres as $genre) {
			$this->genre($genre);
		}
	}

	public function genre($genre)
	{
		$this->addUrl(
			route('genres.show', $genre->getIdWithSlug()), now(), 'daily', 0.6
		);
	}

	public function authors()
	{
		$this->info('authors');

		$bar = $this->output->createProgressBar(Author::count());

		$bar->start();

		Author::when(!empty($this->olderThanDate), function ($query) {
			$query->where('created_at', '>', $this->olderThanDate);
		})
			->chunkById(1000, function ($items) use ($bar) {
				foreach ($items as $item) {
					$this->author($item);

					$bar->advance();
				}
			});

		$bar->finish();
		$this->info('');
	}

	public function author($author)
	{
		$this->addUrl(
			route('authors.show', $author),
			$author->user_edited_at, 'weekly', 0.6
		);
	}

	public function sequences()
	{
		$this->info('sequences');

		$bar = $this->output->createProgressBar(Sequence::count());

		$bar->start();

		Sequence::when(!empty($this->olderThanDate), function ($query) {
			$query->where('created_at', '>', $this->olderThanDate);
		})->chunkById(1000, function ($items) use ($bar) {
			foreach ($items as $item) {
				$this->sequence($item);
				$bar->advance();
			}
		});

		$bar->finish();
		$this->info('');
	}

	public function sequence($sequence)
	{
		$this->addUrl(
			route('sequences.show', $sequence),
			$sequence->user_edited_at,
			'weekly', 0.6
		);
	}

	public function keywords()
	{
		$this->info('keywords');

		$this->addUrl(
			route('keywords.index'), now(), 'weekly', 0.8
		);

		$keywords = Keyword::orderBy('text', 'asc')
			->when(!empty($this->olderThanDate), function ($query) {
				$query->where('created_at', '>', $this->olderThanDate);
			})
			->paginate();

		if ($keywords->total() > 0) {
			$keywords->withPath(route('keywords.index'));

			for ($page = 1; $page <= $keywords->lastPage(); $page++) {
				if ($page > 1) {
					$this->addUrl(
						$keywords->url($page)
					);
				}
			}
		}

		Keyword::when(!empty($this->olderThanDate), function ($query) {
			$query->where('created_at', '>', $this->olderThanDate);
		})->chunkById(1000, function ($items) {
			foreach ($items as $item) {
				$this->keyword_books($item);
			}
		});
	}

	public function keyword_books($keyword)
	{
		$this->addUrl(
			route('books', ['kw' => $keyword->text])
		);
	}

	public function books()
	{
		$this->info('books');

		$bar = $this->output->createProgressBar(Book::accepted()->count());

		$bar->start();

		Book::accepted()
			->when(!empty($this->olderThanDate), function ($query) {
				$query->where('created_at', '>', $this->olderThanDate);
			})
			->chunkById(1000, function ($items) use ($bar) {
				foreach ($items as $item) {
					$this->book($item);
					$this->book_comments($item);

					if ($item->isReadAccess()) {
						$this->book_pages($item);
					}

					$bar->advance();
				}
			});

		$bar->finish();
		$this->info('');
	}

	public function book($book)
	{
		$this->addUrl(
			route('books.show', $book),
			$book->user_updated_at, 'weekly', 0.6
		);
	}

	public function book_comments($book)
	{
		if (!empty($book->group))
			$query = Comment::whereIn('commentable_id', $book->group->books->pluck('id')->toArray())
				->where('commentable_type', 'book');
		else
			$query = $book->comments();

		$top_comments = (clone $query)
			->roots()
			->latest()
			->limit(33)
			->get()
			->sortByDesc('vote')
			->where('vote', '>', '1')
			->take(2);

		$top_comments_ids = $top_comments->pluck('id')->toArray();

		$comments = (clone $query)
			->when(!empty($top_comments_ids), function ($query) use ($top_comments_ids) {
				return $query->whereNotIn('id', $top_comments_ids);
			})
			->roots()
			->latest()
			->paginate(config('litlife.comments_on_page_count'));

		$comments->withPath(route('books.show', $book));

		if ($comments->total() > 0) {
			for ($page = 1; $page <= $comments->lastPage(); $page++) {
				if ($page > 1) {
					$this->addUrl(
						$comments->url($page), now(),
						'weekly', 0.6
					);
				}
			}
		}
	}

	public function book_pages($book)
	{
		if ($book->isPagesNewFormat()) {

			if ($book->page_count > 0) {
				$this->addUrl(
					route('books.sections.index', $book), '', '', ''
				);

				$sections = $book->sections()->get();

				foreach ($sections as $section) {
					$this->addUrl(
						route('books.sections.show', ['book' => $book, 'section' => $section->inner_id]), '', '', ''
					);

					$pages = $section->pages()
						->orderBy('page', 'asc')
						->paginate();

					$pages->withPath(route('books.sections.show', ['book' => $book, 'section' => $section->inner_id]));

					foreach ($pages as $page) {
						if ($page->page > 1) {
							$this->addUrl(
								$pages->url($page->page), '', '', ''
							);
						}
					}
				}
			}

		} else {

			if ($book->page_count > 0) {
				$this->addUrl(
					route('books.old.page', ['book' => $book]), '', '', ''
				);

				for ($page = 1; $page <= $book->page_count; $page++) {
					if ($page > 1) {
						$this->addUrl(
							route('books.old.page', ['book' => $book, 'page' => $page]), '', '', ''
						);
					}
				}
			}
		}
	}

	public function users()
	{
		$this->info('users');

		$bar = $this->output->createProgressBar(User::count());

		$bar->start();

		User::when(!empty($this->olderThanDate), function ($query) {
			$query->where('created_at', '>', $this->olderThanDate);
		})->chunkById(1000, function ($items) use ($bar) {
			foreach ($items as $item) {
				$this->user($item);
				//$this->user_wall($item);
				$bar->advance();
			}
		});

		$bar->finish();
		$this->info('');
	}

	public function user($user)
	{
		$this->addUrl(
			route('profile', $user), now(), 'weekly', 0.6
		);
	}

	public function forums()
	{
		$this->info('forums');

		$this->addUrl(route('forums.index'), now(), 'hourly', 0.8);

		$bar = $this->output->createProgressBar(Forum::public()->count());

		$bar->start();

		Forum::public()
			->when(!empty($this->olderThanDate), function ($query) {
				$query->where('created_at', '>', $this->olderThanDate);
			})
			->chunkById(1000, function ($items) use ($bar) {
				foreach ($items as $item) {
					$this->forum($item);
					$bar->advance();
				}
			});

		$bar->finish();
		$this->info('');
	}

	public function forum($forum)
	{
		$this->addUrl(
			route('forums.show', $forum), now(), 'weekly', 0.6
		);

		$topics = $forum->topics()->paginate();

		if ($topics->total() > 0) {
			$topics->withPath(route('forums.show', $forum));

			for ($page = 1; $page <= $topics->lastPage(); $page++) {
				if ($page > 1) {
					$this->addUrl(
						$topics->url($page), now(), 'weekly', 0.6
					);
				}
			}
		}
	}

	public function topics()
	{
		$query = Topic::when(!empty($this->olderThanDate), function ($query) {
			$query->where('created_at', '>', $this->olderThanDate);
		});

		$bar = $this->output->createProgressBar($query->count());

		$bar->start();

		$query->chunkById(1000, function ($items) use ($bar) {
			foreach ($items as $item) {
				$this->topic($item);
			}
		});

		$bar->finish();
		$this->info('');
	}

	public function topic($topic)
	{
		$this->addUrl(
			route('topics.show', $topic), now(), 'daily', 0.4
		);

		if ($topic->top_post_id)
			$top_post = $topic->top_post;
		else
			$top_post = null;

		$posts = $topic->posts()
			->roots()
			->when($top_post, function ($query) use ($top_post) {
				return $query->where('id', '!=', $top_post->id);
			})
			->when($topic->post_desc, function ($query) use ($topic) {
				return $query->latest();
			}, function ($query) {
				return $query->oldest();
			})
			->paginate();

		if ($posts->total() > 0) {
			$posts->withPath(route('topics.show', $topic));

			for ($page = 1; $page <= $posts->lastPage(); $page++) {
				if ($page > 1) {
					$this->addUrl(
						$posts->url($page), now(), 'daily', 0.4
					);
				}
			}
		}
	}

	public function saveSitemapIndexToFile()
	{
		$this->saveCurrentSitemap();

		if (Storage::disk($this->storage)->exists($this->sitemapDirname . '/sitemap.xml'))
			Storage::disk($this->storage)->delete($this->sitemapDirname . '/sitemap.xml');

		Storage::disk($this->storage)
			->put($this->sitemapDirname . '/sitemap.xml', $this->sitemapIndex->getContent());
	}

	public function sendToSearchEngine()
	{
		$host = Url::fromString(config('app.url'))->getHost();

		$this->ping((string)Url::fromString('http://google.com/ping?sitemap=https://' . $host . '/sitemap.xml'));
		$this->ping((string)Url::fromString('http://webmaster.yandex.ru/ping?sitemap=https://' . $host . '/sitemap.xml'));
	}

	public function ping($url)
	{
		$headers = [
			'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/66.0.3359.170 Safari/537.36'
		];

		$client = new Client();

		$response = $client->request('GET', $url, [
			'allow_redirects' => [
				'max' => 5,        // allow at most 10 redirects.
				'strict' => false,      // use "strict" RFC compliant redirects.
				'referer' => true,      // add a Referer header
			],
			'connect_timeout' => 5,
			'read_timeout' => 10,
			'headers' => $headers,
			'timeout' => 20
		])->getBody();
	}

	public function getSitemapIndex()
	{
		return $this->sitemapIndex;
	}

	public function user_wall($user)
	{
		if (!empty($user->setting))
			$top_blog_record = $user->setting->top_blog_record;

		$blogs = $user->blog()
			->when($top_blog_record, function ($query, $top_blog_record) {
				$query->where('id', '!=', $top_blog_record->id);
			})
			->roots()
			->paginate();

		if ($blogs->total() > 0) {
			$blogs->withPath(route('profile', $user));

			for ($page = 1; $page <= $blogs->lastPage(); $page++) {
				if ($page > 1) {
					$this->addUrl(
						$blogs->url($page), now(), 'weekly', 0.6
					);
				}
			}
		}
	}
}
