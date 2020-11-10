<?php

namespace App;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\AuthorAverageRatingForPeriod
 *
 * @property int $author_id
 * @property int|null $day_rating
 * @property int|null $week_rating
 * @property int|null $month_rating
 * @property int|null $quarter_rating
 * @property int|null $year_rating
 * @property int|null $all_rating
 * @property-read Author $author
 * @method static Builder|AuthorAverageRatingForPeriod newModelQuery()
 * @method static Builder|AuthorAverageRatingForPeriod newQuery()
 * @method static Builder|AuthorAverageRatingForPeriod query()
 * @method static Builder|AuthorAverageRatingForPeriod whereAllRating($value)
 * @method static Builder|AuthorAverageRatingForPeriod whereAuthorId($value)
 * @method static Builder|AuthorAverageRatingForPeriod whereDayRating($value)
 * @method static Builder|AuthorAverageRatingForPeriod whereMonthRating($value)
 * @method static Builder|AuthorAverageRatingForPeriod whereQuarterRating($value)
 * @method static Builder|AuthorAverageRatingForPeriod whereWeekRating($value)
 * @method static Builder|AuthorAverageRatingForPeriod whereYearRating($value)
 * @mixin Eloquent
 */
class AuthorAverageRatingForPeriod extends Model
{
    public $attributes = [
        'day_rating' => 0,
        'week_rating' => 0,
        'month_rating' => 0,
        'quarter_rating' => 0,
        'year_rating' => 0,
        'all_rating' => 0
    ];

    public $timestamps = false;
    public $incrementing = false;
    protected $primaryKey = 'author_id';

    public function author()
    {
        return $this->belongsTo('App\Author');
    }
}
