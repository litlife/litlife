<?php

namespace App\Console\Commands\Refresh;

use App\Author;
use App\AuthorAverageRatingForPeriod;
use App\Jobs\Author\UpdateAuthorRating;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;

class RefreshAuthorsDailyRating extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'refresh:authors_daily_rating {latest_id=0}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Комманда обновляет все рейтинги авторов у которых не нулевой рейтинг за день';

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		AuthorAverageRatingForPeriod::with('author')
			->whereHas('author', function (Builder $query) {
				$query->notMerged();
			})
			->where('day_rating', '>', 0)
			->where('author_id', '>=', $this->argument('latest_id'))
			->chunkById(100, function ($items) {
				foreach ($items as $item) {
					$this->info('author: ' . $item->author->id);
					$this->item($item->author);
				}
			});
	}

	public function item(Author $item)
	{
		UpdateAuthorRating::dispatch($item);
	}
}
