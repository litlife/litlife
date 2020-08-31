<?php

namespace App\Console\Commands\Old;

use App\Bookmark;
use Illuminate\Console\Command;
use Litlife\Url\Url;

class BookmarksRoutes extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'to_new:bookmarks_routes {limit=1000}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Обрабатывает маршруты в закладках';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		Bookmark::chunk($this->argument('limit'), function ($items) {
			foreach ($items as $item) {
				$this->item($item);
			}
		});
	}

	function item($bookmark)
	{
		echo('bm ' . $bookmark->id . "\n");

		$bookmark->url = html_entity_decode($bookmark->url);

		$url_new = $this->chopUrl($bookmark->url);

		if (empty($url_new)) {

			$url = Url::fromString($bookmark->url);

			if (!empty($url->getQueryParameter('book'))) {
				if (!empty($url->getQueryParameter('description'))) {
					$url_new = '/books/' . $url->getQueryParameter('book');
				} elseif (!empty($url->getQueryParameter('page'))) {
					$url_new = '/books/' . $url->getQueryParameter('book') . '/read?page=' . $url->getQueryParameter('page');
				} else {
					$url_new = '/books/' . $url->getQueryParameter('book') . '/read';
				}
			}
		}

		$bookmark->url_new = $url_new;
		$bookmark->new = true;
		$bookmark->save();
	}

	function chopUrl($url)
	{
		$url = Url::fromString($url);

		preg_match('/(\/*)([A-z]+)(\/*)/iu', $url->getPath(), $matches);

		if (!empty($matches['2'])) {
			switch ($matches['2']) {
				case 'a':
					return '/authors/' . $url->getQueryParameter('id');
					break;

				case 'bd':
					return '/books/' . $url->getQueryParameter('b');
					break;

				case 'books_in_series':
					return '/sequences/' . $url->getQueryParameter('id');
					break;

				case 'br':
					if (!empty($url->getQueryParameter('p')))
						return '/books/' . $url->getQueryParameter('b') . '/read?page=' . $url->getQueryParameter('p');
					else
						return '/books/' . $url->getQueryParameter('b') . '/read';
					break;

				case 'Topic':
					return '/topics/' . $url->getQueryParameter('Id');
					break;

				case 'add_book_fb2':
					return '/books/create';
					break;

				case 'ab':
					return '/books/create';
					break;
			}
		}

	}
}
