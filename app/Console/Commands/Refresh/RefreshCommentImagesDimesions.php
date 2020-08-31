<?php

namespace App\Console\Commands\Refresh;

use App\Comment;
use App\Jobs\CommentImageDimensioning;
use Illuminate\Console\Command;

class RefreshCommentImagesDimesions extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'refresh:comment_images_dimesions';

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
		Comment::any()->orderBy('id', 'desc')->chunk(1000, function ($items) {
			foreach ($items as $item) {
				echo('comemnt: ' . $item->id . "\n");

				CommentImageDimensioning::dispatch($item);
			}
		});
	}


}
