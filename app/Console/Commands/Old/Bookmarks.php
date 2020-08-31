<?php

namespace App\Console\Commands\Old;

use App\Bookmark;
use App\BookmarkFolder;
use Illuminate\Console\Command;

class Bookmarks extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'to_new:bookmarks {limit=1000}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Обрабатывает закладки';

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
		Bookmark::distinct()->whereNull('folder_id')->chunk($this->argument('limit'), function ($bookmarks) {
			foreach ($bookmarks as $bookmark) {
				if (!BookmarkFolder::where('title', 'Без папки')->where('user_id', $bookmark->user_id)->first()) {
					$folder = new BookmarkFolder;
					$folder->title = 'Без папки';
					$folder->user_id = $bookmark->user_id;
					$folder->save();
				}
			}

		});

		$folders = BookmarkFolder::where('title', 'Без папки')->get();

		foreach ($folders as $folder) {
			Bookmark::whereNull('folder_id')
				->where('user_id', $folder->user_id)
				->update(['folder_id' => $folder->id]);
		}

		$limit = $this->argument('limit');

		Bookmark::orderBy('id')->chunk($limit, function ($items) {
			foreach ($items as $item) {
				$this->item($item);
			}
		});
	}

	function item($bookmark)
	{
		$title = $bookmark->title;

		$title = str_replace(" - Litmir.net", "", $title);
		$title = str_replace(" - ЛитМир.net", "", $title);
		$title = str_replace(" - ЛитМир", "", $title);
		$title = str_replace(" - Читать, Скачать - Litru.ru", "", $title);
		$title = str_replace(" - Читать, Скачать, Купить - Litru.ru", "", $title);
		$title = str_replace(" - Litru.ru", "", $title);

		$bookmark->title = $title;

		echo $bookmark->create_user_id . ' ' . $title . "\n";

		$bookmark->save();
	}
}
