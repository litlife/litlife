<?php

namespace App\Console\Commands\Refresh;

use App\Jobs\PostImageDimensioning;
use App\Post;
use Illuminate\Console\Command;

class RefreshPostImagesDimesions extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'refresh:post_images_dimesions';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Пересчитаем у изображений отсутствующие размеры';

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
		Post::any()->orderBy('id', 'desc')->chunk(1000, function ($items) {
			foreach ($items as $item) {
				echo('post: ' . $item->id . "\n");

				PostImageDimensioning::dispatch($item);
			}
		});
	}


}
