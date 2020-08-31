<?php

namespace App\Console\Commands\Refresh;

use App\Blog;
use App\Comment;
use App\Jobs\DownloadExternalImages;
use App\Message;
use App\Post;
use Illuminate\Console\Command;

class RefreshDownloadExternalImages extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'refresh:download_external_images {limit=1000}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Ищем в сообщениях внешние изображение и закачиваем их на наш сервер';

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
		Blog::any()
			->where('external_images_downloaded', false)
			->chunkById($this->argument('limit'), function ($items) {
				foreach ($items as $item) {
					$this->info('blog: ' . $item->id . "");

					$item->bb_text = trim($item->bb_text);

					if (empty($item->external_images_downloaded))
						$item->save();
				}
			});

		Comment::any()
			->where('external_images_downloaded', false)
			->chunkById($this->argument('limit'), function ($items) {
				foreach ($items as $item) {
					$this->info('comment: ' . $item->id . "");

					$item->bb_text = trim($item->bb_text);

					if (empty($item->external_images_downloaded))
						$item->save();
				}
			});

		Post::any()
			->where('external_images_downloaded', false)
			->chunkById($this->argument('limit'), function ($items) {
				foreach ($items as $item) {
					$this->info('post: ' . $item->id . "");

					$item->bb_text = trim($item->bb_text);

					if (empty($item->external_images_downloaded))
						$item->save();
				}
			});

		Message::where('external_images_downloaded', false)
			->chunkById($this->argument('limit'), function ($items) {
				foreach ($items as $item) {
					$this->info('message: ' . $item->id . "");

					$item->bb_text = trim($item->bb_text);

					if (empty($item->external_images_downloaded))
						$item->save();
				}
			});
	}


}
