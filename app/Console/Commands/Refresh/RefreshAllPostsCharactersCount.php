<?php

namespace App\Console\Commands\Refresh;

use App\Post;
use Illuminate\Console\Command;

class RefreshAllPostsCharactersCount extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'refresh:all_posts_characters_count {limit=1000}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Комманда обновляет количество символов у всех сообщений на форуме';

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		Post::any()->chunkById($this->argument('limit'), function ($items) {
			foreach ($items as $item) {
				$this->item($item);
			}
		});
	}

	function item(Post $post)
	{
		echo("$post->id \n");
		$post->refreshCharactersCount();
		$post->save();
	}
}
