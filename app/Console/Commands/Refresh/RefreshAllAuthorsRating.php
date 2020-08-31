<?php

namespace App\Console\Commands\Refresh;

use App\Author;
use App\Jobs\Author\UpdateAuthorRating;
use Illuminate\Console\Command;

class RefreshAllAuthorsRating extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'refresh:all_author_rating {limit=500} {latest_id=0}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Комманда обновляет рейтинг всех авторов';

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		Author::notMerged()
			->where('id', '>', $this->argument('latest_id'))
			->chunkById($this->argument('limit'), function ($items) {
				foreach ($items as $item) {
					$this->info('author: ' . $item->id);

					UpdateAuthorRating::dispatch($item);
				}
			});
	}
}
