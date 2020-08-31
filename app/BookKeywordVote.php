<?php

namespace App;

use App\Model as Model;
use App\Traits\UserCreate;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

/**
 * App\BookKeywordVote
 *
 * @property int $book_keyword_id
 * @property int $create_user_id
 * @property int $vote
 * @property int $time
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $id
 * @property-read \App\BookKeyword $book_keyword
 * @property-read \App\User $create_user
 * @method static Builder|BookKeywordVote newModelQuery()
 * @method static Builder|BookKeywordVote newQuery()
 * @method static Builder|Model orderByField($column, $ids)
 * @method static Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
 * @method static Builder|BookKeywordVote query()
 * @method static Builder|Model void()
 * @method static Builder|BookKeywordVote whereBookKeywordId($value)
 * @method static Builder|BookKeywordVote whereCreateUserId($value)
 * @method static Builder|BookKeywordVote whereCreatedAt($value)
 * @method static Builder|BookKeywordVote whereCreator(\App\User $user)
 * @method static Builder|BookKeywordVote whereId($value)
 * @method static Builder|BookKeywordVote whereTime($value)
 * @method static Builder|BookKeywordVote whereUpdatedAt($value)
 * @method static Builder|BookKeywordVote whereVote($value)
 * @mixin Eloquent
 */
class BookKeywordVote extends Model
{
	use UserCreate;

	protected $fillable = [
		'book_keyword_id',
		'vote'
	];

	public static function boot()
	{
		static::Creating(function ($model) {
			$model->autoAssociateAuthUser();
		});

		parent::boot();
	}

	public function book_keyword()
	{
		return $this->belongsTo('App\BookKeyword');
	}
}
