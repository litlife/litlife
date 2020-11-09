<?php

namespace App;

use App\Model as Model;

/**
 * App\UserFavoriteCollection
 *
 * @property int $id
 * @property int $collection_id ID подборки
 * @property int $user_id ID пользователя
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Collection $collection
 * @property-read \App\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|UserFavoriteCollection newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserFavoriteCollection newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByField($column, $ids)
 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
 * @method static \Illuminate\Database\Eloquent\Builder|UserFavoriteCollection query()
 * @method static \Illuminate\Database\Eloquent\Builder|Model void()
 * @method static \Illuminate\Database\Eloquent\Builder|UserFavoriteCollection whereCollectionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserFavoriteCollection whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserFavoriteCollection whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserFavoriteCollection whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserFavoriteCollection whereUserId($value)
 * @mixin \Eloquent
 */
class UserFavoriteCollection extends Model
{
	protected $fillable = [
		'collection_id',
		'user_id'
	];

	public static function boot()
	{
		static::Creating(function ($model) {

			if (empty($model->user_id))
				$model->user_id = auth()->id();
		});

		static::Created(function ($model) {
			$model->user->refreshFavoriteCollectionsCount();
			$model->user->push();
		});

		static::Deleted(function ($model) {
			$model->collection->addedToFavoritesUsersCountRefresh();

			$model->user->refreshFavoriteCollectionsCount();
			$model->user->push();
		});
		/*
				static::Restored(function ($model) {
					$model->collection->addedToFavoritesUsersCountRefresh();

					$model->user->refreshFavoriteCollectionsCount();
					$model->user->push();
				});
		*/
		static::Saved(function ($model) {
			$model->collection->addedToFavoritesUsersCountRefresh();
		});

		parent::boot();
	}

	public function user()
	{
		return $this->belongsTo('App\User', 'user_id', 'id')
			->any();
	}

	public function collection()
	{
		return $this->belongsTo('App\Collection', 'collection_id', 'id')
			->any();
	}
}
