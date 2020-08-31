<?php

namespace App\Console\Commands\Refresh;

use App\Book;
use App\Events\BookFilesCountChanged;
use Illuminate\Console\Command;

class RefreshAllBooksCounters extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'refresh:all_books_counters {limit=1000}  {latest_id=0}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Комманда обновляет счетчики всех книг';

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

					$this->call('refresh:book_counters', [
						'id' => $item->id
					]);
				}
			});
	}
}
