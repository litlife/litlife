<?php

namespace App\Console\Commands\Refresh;

use App\Author;
use App\Book;
use App\Sequence;
use Illuminate\Console\Command;

class RefreshAllFavoriteCounters extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'refresh:all_favorite_counters {limit=1000} {latest_id=0}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Комманда обновляет все счетчики добавленного в избранное';

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		Book::any()
			->where('id', '>=', $this->argument('latest_id'))
			->chunkById($this->argument('limit'), function ($items) {
				foreach ($items as $item) {
					$this->info('book: ' . $item->id);

					$item->addedToFavoritesUsersCountRefresh();
				}
			});

		Author::any()
			->where('id', '>=', $this->argument('latest_id'))
			->chunkById($this->argument('limit'), function ($items) {
				foreach ($items as $item) {
					$this->info('author: ' . $item->id);

					$item->addedToFavoritesUsersCountRefresh();
				}
			});

		Sequence::any()
			->where('id', '>=', $this->argument('latest_id'))
			->chunkById($this->argument('limit'), function ($items) {
				foreach ($items as $item) {
					$this->info('sequence: ' . $item->id);

					$item->addedToFavoritesUsersCountRefresh();
				}
			});
	}
}
