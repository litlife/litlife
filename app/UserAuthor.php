<?php

namespace App;

use App\Jobs\User\UpdateUserFavoriteAuthorsCount;
use App\Model as Model;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

/**
 * App\UserAuthor
 *
 * @property int $user_id
 * @property int $author_id
 * @property int $old_time
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $id
 * @property-read \App\Author|null $author
 * @property-read \App\User|null $user
 * @method static Builder|UserAuthor newModelQuery()
 * @method static Builder|UserAuthor newQuery()
 * @method static Builder|Model orderByField($column, $ids)
 * @method static Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
 * @method static Builder|UserAuthor query()
 * @method static Builder|Model void()
 * @method static Builder|UserAuthor whereAuthorId($value)
 * @method static Builder|UserAuthor whereCreatedAt($value)
 * @method static Builder|UserAuthor whereId($value)
 * @method static Builder|UserAuthor whereOldTime($value)
 * @method static Builder|UserAuthor whereUpdatedAt($value)
 * @method static Builder|UserAuthor whereUserId($value)
 * @mixin Eloquent
 */
class UserAuthor extends Model
{
	protected $fillable = [
		'author_id'
	];

	public static function boot()
	{
		static::Creating(function ($model) {
			if (empty($model->user_id))
				$model->user_id = auth()->id();
		});

		static::Deleted(function ($model) {
			UpdateUserFavoriteAuthorsCount::dispatch($model->user);

			if (!empty($model->author))
				$model->author->addedToFavoritesUsersCountRefresh();
		});

		static::Saved(function ($model) {
			UpdateUserFavoriteAuthorsCount::dispatch($model->user);

			if (!empty($model->author))
				$model->author->addedToFavoritesUsersCountRefresh();
		});

		parent::boot();
	}

	public function user()
	{
		return $this->hasOne('App\User', 'id', 'user_id')
			->any();
	}

	public function author()
	{
		return $this->hasOne('App\Author', 'id', 'author_id')
			->any();
	}
}
