<?php

namespace App\Console\Commands\Refresh;

use App\Book;
use App\Events\BookRatingChanged;
use App\Jobs\Book\UpdateBookRating;
use Illuminate\Console\Command;

class RefreshBookVote extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'refresh:book_votes {limit=1000}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Комманда обновляет рейтинг книг';

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		Book::any()->orderBy('id')->chunk($this->argument('limit'), function ($items) {
			foreach ($items as $item) {
				$this->item($item);
			}
		});
	}

	function item($item)
	{
		echo('Book ' . $item->id . "\n");

		UpdateBookRating::dispatch($item);
	}
}
