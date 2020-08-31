<?php

namespace App\Console\Commands\Refresh;


use App\Blog;
use App\Jobs\BlogImageDimensioning;
use Illuminate\Console\Command;

class RefreshWallImagesDimesions extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'refresh:wall_images_dimesions';

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
		Blog::any()->orderBy('id', 'desc')->chunk(1000, function ($items) {
			foreach ($items as $item) {
				echo('blog: ' . $item->id . "\n");

				BlogImageDimensioning::dispatch($item);
			}
		});
	}


}
