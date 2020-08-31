<?php

namespace App\Console\Commands\Refresh;

use App\Blog;
use App\Comment;
use App\Events\BookFilesCountChanged;
use App\Message;
use App\Post;
use Illuminate\Console\Command;

class RefreshHtmlFromBBCode extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'refresh:html_from_bb_code {limit=1000}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Комманда обновляет везде html код на основе bb кода';

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		$limit = $this->argument('limit');

		Blog::any()
			->where('text', 'ilike', '%[font%')
			->chunkById($this->argument('limit'), function ($items) {
				foreach ($items as $item) {
					echo("blog " . $item->id . "\n");

					$item->bb_text = trim($item->bb_text);
					$item->save();
				}
			});

		Comment::any()
			->where('text', 'ilike', '%[font%')
			->chunkById($this->argument('limit'), function ($items) {
				foreach ($items as $item) {
					echo("comment " . $item->id . "\n");
					$item->bb_text = trim($item->bb_text);
					$item->save();
				}
			});

		Message::void()
			->where('text', 'ilike', '%[font%')
			->chunkById($this->argument('limit'), function ($items) {
				foreach ($items as $item) {
					echo("message " . $item->id . "\n");
					$item->bb_text = trim($item->bb_text);
					$item->save();
				}
			});

		Post::any()
			->where('html_text', 'ilike', '%[font%')
			->chunkById($this->argument('limit'), function ($items) {
				foreach ($items as $item) {
					echo("post " . $item->id . "\n");
					$item->bb_text = trim($item->bb_text);
					$item->save();
				}
			});
	}
}
