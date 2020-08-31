<?php

namespace App;

use App\Model as Model;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;

/**
 * App\AuthorStatus
 *
 * @property int $author_id
 * @property int $user_id
 * @property int $code
 * @property int $id
 * @property string|null $user_updated_at Время последнего изменения статуса пользователем
 * @property int $status
 * @property-read \App\Author $author
 * @property-read \App\User $user
 * @method static Builder|AuthorStatus newModelQuery()
 * @method static Builder|AuthorStatus newQuery()
 * @method static Builder|Model orderByField($column, $ids)
 * @method static Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
 * @method static Builder|AuthorStatus query()
 * @method static Builder|Model void()
 * @method static Builder|AuthorStatus whereAuthorId($value)
 * @method static Builder|AuthorStatus whereCode($value)
 * @method static Builder|AuthorStatus whereId($value)
 * @method static Builder|AuthorStatus whereStatus($value)
 * @method static Builder|AuthorStatus whereUserId($value)
 * @method static Builder|AuthorStatus whereUserUpdatedAt($value)
 * @mixin Eloquent
 */
class AuthorStatus extends Model
{
	public $timestamps = false;
	protected $fillable = [
		'author_id',
		'user_id',
		'status',
		'user_updated_at'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'user_id', 'id');
	}

	public function author()
	{
		return $this->belongsTo('App\Author', 'author_id', 'id');
	}
}
