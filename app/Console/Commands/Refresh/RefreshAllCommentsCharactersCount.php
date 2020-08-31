<?php

namespace App\Console\Commands\Refresh;

use App\Comment;
use Illuminate\Console\Command;

class RefreshAllCommentsCharactersCount extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'refresh:all_comments_characters_count {limit=1000}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Комманда обновляет количество символов у всех комментариев';

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		Comment::any()->chunkById($this->argument('limit'), function ($items) {
			foreach ($items as $item) {
				$this->item($item);
			}
		});
	}

	function item(Comment $comment)
	{
		echo("$comment->id \n");
		$comment->refreshCharactersCount();
		$comment->save();
	}
}
