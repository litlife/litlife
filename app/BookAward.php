<?php

namespace App;

use App\Model as Model;
use App\Traits\UserCreate;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

/**
 * App\BookAward
 *
 * @property int $id
 * @property int $book_id
 * @property int $award_id
 * @property int|null $year
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $create_user_id
 * @property-read \App\Award $award
 * @property-read \App\Book $book
 * @property-read \App\User $create_user
 * @method static Builder|BookAward newModelQuery()
 * @method static Builder|BookAward newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByField($column, $ids)
 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
 * @method static Builder|BookAward query()
 * @method static \Illuminate\Database\Eloquent\Builder|Model void()
 * @method static Builder|BookAward whereAwardId($value)
 * @method static Builder|BookAward whereBookId($value)
 * @method static Builder|BookAward whereCreateUserId($value)
 * @method static Builder|BookAward whereCreatedAt($value)
 * @method static Builder|BookAward whereCreator(\App\User $user)
 * @method static Builder|BookAward whereId($value)
 * @method static Builder|BookAward whereUpdatedAt($value)
 * @method static Builder|BookAward whereYear($value)
 * @mixin Eloquent
 */
class BookAward extends Model
{
	use UserCreate;

	public $incrementing = false;
	protected $fillable = [
		'year',
		'award_id',
		'book_id'
	];
	protected $primaryKey = [
		'book_id',
		'award_id'
	];

	public function award()
	{
		return $this->belongsTo('App\Award');
	}

	public function book()
	{
		return $this->belongsTo('App\Book')->any();
	}


}
