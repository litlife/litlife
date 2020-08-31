<?php

namespace App\Console\Commands\Refresh;

use App\Book;
use App\Events\BookFilesCountChanged;
use App\Jobs\Book\UpdateBookRating;
use Illuminate\Console\Command;

class RefreshAllBooksRating extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'refresh:all_books_rating {limit=500} {latest_id=0}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Комманда обновляет рейтинг всех книг главных изданий';

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		Book::any()
			->notConnected()
			->where('id', '>', $this->argument('latest_id'))
			->chunkById($this->argument('limit'), function ($items) {
				foreach ($items as $item) {
					$this->info('book: ' . $item->id);

					UpdateBookRating::dispatch($item);
				}
			});
	}
}
