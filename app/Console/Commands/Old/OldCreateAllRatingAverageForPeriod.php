<?php

namespace App\Console\Commands\Old;

use App\Book;
use Illuminate\Console\Command;

class OldCreateAllRatingAverageForPeriod extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'to_new:create_all_books_rating_average_for_period';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Комманда заполняет для всех книг данные в таблице books_rating_average_for_period';

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		Book::chunkById(1000, function ($books) {
			foreach ($books as $book) {
				echo($book->id . "\n");

				$book->average_rating_for_period->save();
			}
		});
	}
}
