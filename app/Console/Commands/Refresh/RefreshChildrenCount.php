<?php

namespace App\Console\Commands\Refresh;

use App\Blog;
use App\Comment;
use App\Post;
use Illuminate\Console\Command;

class RefreshChildrenCount extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'refresh:children_count';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Комманда обновляет количество подсообщений';

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		Blog::orderBy('id')->whereNotNull('tree')->chunk(100, function ($items) {
			foreach ($items as $item) {
				@$this->item($item);
			}
		});

		Comment::orderBy('id')->whereNotNull('tree')->chunk(100, function ($items) {
			foreach ($items as $item) {
				@$this->item($item);
			}
		});

		Post::orderBy('id')->whereNotNull('tree')->chunk(100, function ($items) {
			foreach ($items as $item) {
				@$this->item($item);
			}
		});
	}

	function item($item)
	{
		echo($item->id . "\n");

		$item->updateChildrenCount();
	}
}
