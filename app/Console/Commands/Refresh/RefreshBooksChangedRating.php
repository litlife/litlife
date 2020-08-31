<?php

namespace App\Console\Commands\Refresh;

use App\Book;
use App\Events\BookFilesCountChanged;
use App\Jobs\Book\UpdateBookRating;
use Illuminate\Console\Command;

class RefreshBooksChangedRating extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'refresh:books_changed_rating';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Update books raiting that are changed';

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		Book::any()->where("refresh_rating", true)
			->chunkById(100, function ($books) {
				foreach ($books as $book) {
					UpdateBookRating::dispatch($book);
				}
			});
	}
}
