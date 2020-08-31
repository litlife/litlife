<?php

namespace App\Console\Commands\Refresh;

use App\Comment;
use Illuminate\Console\Command;

class RefreshAllCommentsVotes extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'refresh:all_comments_votes {limit=1000}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Комманда обновляет количество голосов у комментариев';

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

	function item($comment)
	{
		echo("$comment->id \n");
		$comment->updateVotes();
	}
}
