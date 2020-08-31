<?php

namespace App\Console\Commands\Refresh;

use App\Book;
use App\Events\BookFilesCountChanged;
use App\Jobs\Book\UpdateBookFilesCount;
use Illuminate\Console\Command;

class RefreshBookFilesCount extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'refresh:book_files_count {limit=1000}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Комманда обновляет количество файлов у каждой книги';

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		Book::any()->chunkById($this->argument('limit'), function ($items) {
			foreach ($items as $item) {
				$this->item($item);
			}
		});
	}

	function item($item)
	{
		echo("Book: " . $item->id . "\n");

		UpdateBookFilesCount::dispatch($item);
	}
}
