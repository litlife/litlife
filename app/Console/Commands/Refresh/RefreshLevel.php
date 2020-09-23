<?php

namespace App\Console\Commands\Refresh;

use App\Blog;
use App\Comment;
use App\Post;
use Illuminate\Console\Command;

class RefreshLevel extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'refresh:level';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Комманда обновляет уровень комментариев в блоге, в книгах и сообщениях на форуме';

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		Blog::orderBy('id')->whereNotNull('tree')->chunk(1000, function ($items) {
			foreach ($items as $item) {
				@$this->item($item);
			}
		});

		Comment::orderBy('id')->whereNotNull('tree')->chunk(1000, function ($items) {
			foreach ($items as $item) {
				@$this->item($item);
			}
		});

		Post::orderBy('id')->whereNotNull('tree')->chunk(1000, function ($items) {
			foreach ($items as $item) {
				@$this->item($item);
			}
		});
	}

	function item($item)
	{
		echo($item->id . "\n");

		$item->updateLevel();
		$item->save();
	}
}
