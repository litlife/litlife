<?php

namespace App;

use App\Model as Model;
use App\Traits\UserCreate;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

/**
 * App\BookSimilarVote
 *
 * @property int $create_user_id
 * @property int $vote
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $book_id
 * @property int $other_book_id
 * @property int $id
 * @property-read \App\Book $book
 * @property-read \App\User $create_user
 * @property-read \App\Book $other_book
 * @method static Builder|BookSimilarVote newModelQuery()
 * @method static Builder|BookSimilarVote newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByField($column, $ids)
 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
 * @method static Builder|BookSimilarVote query()
 * @method static \Illuminate\Database\Eloquent\Builder|Model void()
 * @method static Builder|BookSimilarVote whereBookId($value)
 * @method static Builder|BookSimilarVote whereCreateUserId($value)
 * @method static Builder|BookSimilarVote whereCreatedAt($value)
 * @method static Builder|BookSimilarVote whereCreator(\App\User $user)
 * @method static Builder|BookSimilarVote whereId($value)
 * @method static Builder|BookSimilarVote whereOtherBookId($value)
 * @method static Builder|BookSimilarVote whereUpdatedAt($value)
 * @method static Builder|BookSimilarVote whereVote($value)
 * @mixin Eloquent
 */
class BookSimilarVote extends Model
{
	use UserCreate;

	protected $fillable = [
		'vote',
		'book_id',
		'other_book_id'
	];

	public static function boot()
	{
		static::Creating(function ($model) {

			$model->autoAssociateAuthUser();

		});

		parent::boot();
	}

	public function setVoteAttribute($value)
	{
		$value = intval($value);

		if ($value > 0)
			$this->attributes['vote'] = 1;
		elseif ($value < 0)
			$this->attributes['vote'] = '-1';
		else
			$this->attributes['vote'] = 0;
	}

	public function book()
	{
		return $this->belongsTo('App\Book', 'book_id', 'id');
	}

	public function other_book()
	{
		return $this->belongsTo('App\Book', 'other_book_id', 'id');
	}
}
