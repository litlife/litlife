<?php

namespace App;

use App\Model as Model;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;

/**
 * App\BookAverageRatingForPeriod
 *
 * @property int $book_id
 * @property float $day_vote_average
 * @property int $day_votes_count
 * @property int $week_rating
 * @property int $week_votes_count
 * @property int $month_rating
 * @property int $month_votes_count
 * @property int $quarter_rating
 * @property int $quarter_votes_count
 * @property int $year_rating
 * @property int $year_votes_count
 * @property int $day_rating
 * @property float $week_vote_average
 * @property float $month_vote_average
 * @property float $quarter_vote_average
 * @property float $year_vote_average
 * @property int $all_rating
 * @method static Builder|BookAverageRatingForPeriod newModelQuery()
 * @method static Builder|BookAverageRatingForPeriod newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByField($column, $ids)
 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
 * @method static Builder|BookAverageRatingForPeriod query()
 * @method static \Illuminate\Database\Eloquent\Builder|Model void()
 * @method static Builder|BookAverageRatingForPeriod whereAllRating($value)
 * @method static Builder|BookAverageRatingForPeriod whereBookId($value)
 * @method static Builder|BookAverageRatingForPeriod whereDayRating($value)
 * @method static Builder|BookAverageRatingForPeriod whereDayVoteAverage($value)
 * @method static Builder|BookAverageRatingForPeriod whereDayVotesCount($value)
 * @method static Builder|BookAverageRatingForPeriod whereMonthRating($value)
 * @method static Builder|BookAverageRatingForPeriod whereMonthVoteAverage($value)
 * @method static Builder|BookAverageRatingForPeriod whereMonthVotesCount($value)
 * @method static Builder|BookAverageRatingForPeriod whereQuarterRating($value)
 * @method static Builder|BookAverageRatingForPeriod whereQuarterVoteAverage($value)
 * @method static Builder|BookAverageRatingForPeriod whereQuarterVotesCount($value)
 * @method static Builder|BookAverageRatingForPeriod whereWeekRating($value)
 * @method static Builder|BookAverageRatingForPeriod whereWeekVoteAverage($value)
 * @method static Builder|BookAverageRatingForPeriod whereWeekVotesCount($value)
 * @method static Builder|BookAverageRatingForPeriod whereYearRating($value)
 * @method static Builder|BookAverageRatingForPeriod whereYearVoteAverage($value)
 * @method static Builder|BookAverageRatingForPeriod whereYearVotesCount($value)
 * @mixin Eloquent
 */
class BookAverageRatingForPeriod extends Model
{
	public $timestamps = false;
	public $incrementing = false;
	protected $table = 'books_average_rating_for_period';
	protected $primaryKey = 'book_id';

	protected $attributes =
		[
			'day_rating' => 0,
			'day_votes_count' => 0,
			'week_rating' => 0,
			'week_votes_count' => 0,
			'month_rating' => 0,
			'month_votes_count' => 0,
			'quarter_rating' => 0,
			'quarter_votes_count' => 0,
			'year_rating' => 0,
			'year_votes_count' => 0
		];
}
