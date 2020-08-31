<?php

namespace App\Console\Commands\Old;

use App\Author;
use Illuminate\Console\Command;

class OldAuthorCreateAllRatingAverageForPeriod extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'to_new:create_all_authors_rating_average_for_period';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Комманда заполняет для всех книг данные в таблице authors_rating_average_for_period';

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		Author::any()
			->chunkById(1000, function ($authors) {
				foreach ($authors as $author) {
					echo($author->id . "\n");

					$author->averageRatingForPeriod->save();
				}
			});
	}
}
