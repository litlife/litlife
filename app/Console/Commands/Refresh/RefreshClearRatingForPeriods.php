<?php

namespace App\Console\Commands\Refresh;

use App\BookAverageRatingForPeriod;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RefreshClearRatingForPeriods extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'refresh:clear_rating_for_periods';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Комманда обнуляет рейтинги книг за периоды';

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		BookAverageRatingForPeriod::where('day_votes_count', '>', '0')
			->whereNotIn('book_id', function ($query) {
				$query->select(DB::raw('DISTINCT ON("book_id") "book_id"'))
					->from('book_votes')
					->where('created_at', '>', now()->subDay());
			})->update([
				'day_vote_average' => 0,
				'day_rating' => 0,
				'day_votes_count' => 0
			]);

		BookAverageRatingForPeriod::where('week_votes_count', '>', '0')
			->whereNotIn('book_id', function ($query) {
				$query->select(DB::raw('DISTINCT ON("book_id") "book_id"'))
					->from('book_votes')
					->where('created_at', '>', now()->subWeek());
			})->update([
				'week_vote_average' => 0,
				'week_rating' => 0,
				'week_votes_count' => 0
			]);

		BookAverageRatingForPeriod::where('month_votes_count', '>', '0')
			->whereNotIn('book_id', function ($query) {
				$query->select(DB::raw('DISTINCT ON("book_id") "book_id"'))
					->from('book_votes')
					->where('created_at', '>', now()->subMonth());
			})->update([
				'month_vote_average' => 0,
				'month_rating' => 0,
				'month_votes_count' => 0
			]);

		BookAverageRatingForPeriod::where('quarter_votes_count', '>', '0')
			->whereNotIn('book_id', function ($query) {
				$query->select(DB::raw('DISTINCT ON("book_id") "book_id"'))
					->from('book_votes')
					->where('created_at', '>', now()->subQuarter());
			})
			->update([
				'quarter_vote_average' => 0,
				'quarter_rating' => 0,
				'quarter_votes_count' => 0
			]);

		BookAverageRatingForPeriod::where('year_votes_count', '>', '0')
			->whereNotIn('book_id', function ($query) {
				$query->select(DB::raw('DISTINCT ON("book_id") "book_id"'))
					->from('book_votes')
					->where('created_at', '>', now()->subYear());
			})->update([
				'year_vote_average' => 0,
				'year_rating' => 0,
				'year_votes_count' => 0
			]);
	}
}
