<?php

namespace App;

use App\Jobs\User\UpdateUserFavoriteBooksCount;
use App\Model as Model;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

/**
 * App\UserBook
 *
 * @property int $user_id
 * @property int $book_id
 * @property int $time
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $id
 * @property-read \App\Book|null $book
 * @property-read \App\User|null $user
 * @method static Builder|UserBook newModelQuery()
 * @method static Builder|UserBook newQuery()
 * @method static Builder|Model orderByField($column, $ids)
 * @method static Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
 * @method static Builder|UserBook query()
 * @method static Builder|Model void()
 * @method static Builder|UserBook whereBookId($value)
 * @method static Builder|UserBook whereCreatedAt($value)
 * @method static Builder|UserBook whereId($value)
 * @method static Builder|UserBook whereTime($value)
 * @method static Builder|UserBook whereUpdatedAt($value)
 * @method static Builder|UserBook whereUserId($value)
 * @mixin Eloquent
 */
class UserBook extends Model
{
	protected $fillable = [
		'book_id',
		'user_id'
	];

	public static function boot()
	{
		static::Creating(function ($model) {

			if (empty($model->user_id))
				$model->user_id = auth()->id();
		});

		static::Deleted(function ($model) {
			UpdateUserFavoriteBooksCount::dispatch($model->user);

			if (!empty($model->book))
				$model->book->addedToFavoritesUsersCountRefresh();
		});

		static::Saved(function ($model) {
			UpdateUserFavoriteBooksCount::dispatch($model->user);

			if (!empty($model->book))
				$model->book->addedToFavoritesUsersCountRefresh();
		});

		parent::boot();
	}

	public function user()
	{
		return $this->hasOne('App\User', 'id', 'user_id')
			->any();
	}

	public function book()
	{
		return $this->hasOne('App\Book', 'id', 'book_id')
			->any();
	}
}
