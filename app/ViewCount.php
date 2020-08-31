<?php

namespace App;

use App\Model as Model;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;

/**
 * App\ViewCount
 *
 * @property int $book_id
 * @property int $all
 * @property int $week
 * @property int $year
 * @property int $month
 * @property int $day
 * @property-read \App\Book $book
 * @method static Builder|ViewCount newModelQuery()
 * @method static Builder|ViewCount newQuery()
 * @method static Builder|Model orderByField($column, $ids)
 * @method static Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
 * @method static Builder|ViewCount query()
 * @method static Builder|Model void()
 * @method static Builder|ViewCount whereAll($value)
 * @method static Builder|ViewCount whereBookId($value)
 * @method static Builder|ViewCount whereDay($value)
 * @method static Builder|ViewCount whereMonth($value)
 * @method static Builder|ViewCount whereWeek($value)
 * @method static Builder|ViewCount whereYear($value)
 * @mixin Eloquent
 */
class ViewCount extends Model
{
	public $timestamps = false;
	public $incrementing = false;
	public $attributes = [
		'day' => 0,
		'week' => 0,
		'month' => 0,
		'year' => 0,
		'all' => 0
	];
	protected $primaryKey = 'book_id';

	public function book()
	{
		return $this->belongsTo('App\Book', 'book_id', 'id');
	}
}
