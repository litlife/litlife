<?php

namespace App;

use App\Model as Model;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

/**
 * App\UserFavoriteCollection
 *
 * @property int $id
 * @property int $collection_id ID подборки
 * @property int $user_id ID пользователя
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read \App\Collection $collection
 * @property-read \App\User $user
 * @method static Builder|UserFavoriteCollection newModelQuery()
 * @method static Builder|UserFavoriteCollection newQuery()
 * @method static Builder|Model orderByField($column, $ids)
 * @method static Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
 * @method static Builder|UserFavoriteCollection query()
 * @method static Builder|Model void()
 * @method static Builder|UserFavoriteCollection whereCollectionId($value)
 * @method static Builder|UserFavoriteCollection whereCreatedAt($value)
 * @method static Builder|UserFavoriteCollection whereId($value)
 * @method static Builder|UserFavoriteCollection whereUpdatedAt($value)
 * @method static Builder|UserFavoriteCollection whereUserId($value)
 * @mixin Eloquent
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

            if (empty($model->user_id)) {
                $model->user_id = auth()->id();
            }
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
