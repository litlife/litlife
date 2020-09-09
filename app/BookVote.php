<?php

namespace App;

use App\Model as Model;
use App\Traits\UserCreate;
use Awobaz\Compoships\Compoships;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * App\BookVote
 *
 * @property int $book_id
 * @property int $create_user_id
 * @property int $old_rate
 * @property int $old_time
 * @property int $old_hide
 * @property int $vote
 * @property string|null $ip
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property int $id
 * @property Carbon $user_updated_at
 * @property int|null $origin_book_id
 * @property-read \App\Book $book
 * @property-read \App\User $create_user
 * @property-read \App\Book|null $originBook
 * @property-read \App\User|null $user
 * @method static Builder|BookVote newModelQuery()
 * @method static Builder|BookVote newQuery()
 * @method static \Illuminate\Database\Query\Builder|BookVote onlyTrashed()
 * @method static Builder|Model orderByField($column, $ids)
 * @method static Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
 * @method static Builder|BookVote query()
 * @method static Builder|BookVote void()
 * @method static Builder|BookVote whereBookId($value)
 * @method static Builder|BookVote whereCreateUserId($value)
 * @method static Builder|BookVote whereCreatedAt($value)
 * @method static Builder|BookVote whereCreator(\App\User $user)
 * @method static Builder|BookVote whereDeletedAt($value)
 * @method static Builder|BookVote whereId($value)
 * @method static Builder|BookVote whereIp($value)
 * @method static Builder|BookVote whereOldHide($value)
 * @method static Builder|BookVote whereOldRate($value)
 * @method static Builder|BookVote whereOldTime($value)
 * @method static Builder|BookVote whereOriginBookId($value)
 * @method static Builder|BookVote whereUpdatedAt($value)
 * @method static Builder|BookVote whereUserUpdatedAt($value)
 * @method static Builder|BookVote whereVote($value)
 * @method static \Illuminate\Database\Query\Builder|BookVote withTrashed()
 * @method static \Illuminate\Database\Query\Builder|BookVote withoutTrashed()
 * @mixin Eloquent
 */
class BookVote extends Model
{
	use UserCreate;
	use SoftDeletes;
	use Compoships;

	public $votes = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];

	protected $fillable = [
		'vote',
		'create_user_id',
		'ip',
		'user_updated_at',
		'origin_book_id'
	];

	protected $dates = [
		'user_updated_at'
	];

	public function scopeVoid($query)
	{
		return $query;
	}

	public function book()
	{
		return $this->belongsTo('App\Book')->any();
	}

	public function originBook()
	{
		return $this->belongsTo('App\Book')->any();
	}

	public function user()
	{
		return $this->hasOne('App\User', 'id', $this->getCreateUserIdColumn());
	}

	public function setVoteAttribute($vote)
	{
		if ($vote > max($this->votes))
			$vote = max($this->votes);

		if ($vote < min($this->votes))
			$vote = min($this->votes);

		$this->attributes['vote'] = $vote;
	}
}
