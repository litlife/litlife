<?php

namespace App;

use App\Jobs\User\UpdateUserFavoriteSequencesCount;
use App\Model as Model;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

/**
 * App\UserSequence
 *
 * @property int $user_id
 * @property int $sequence_id
 * @property int $old_time
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $id
 * @property-read \App\Sequence $sequence
 * @property-read \App\User|null $user
 * @method static Builder|UserSequence newModelQuery()
 * @method static Builder|UserSequence newQuery()
 * @method static Builder|Model orderByField($column, $ids)
 * @method static Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
 * @method static Builder|UserSequence query()
 * @method static Builder|Model void()
 * @method static Builder|UserSequence whereCreatedAt($value)
 * @method static Builder|UserSequence whereId($value)
 * @method static Builder|UserSequence whereOldTime($value)
 * @method static Builder|UserSequence whereSequenceId($value)
 * @method static Builder|UserSequence whereUpdatedAt($value)
 * @method static Builder|UserSequence whereUserId($value)
 * @mixin Eloquent
 */
class UserSequence extends Model
{
	protected $fillable = [
		'sequence_id'
	];

	public static function boot()
	{
		static::Creating(function ($model) {
			if (empty($model->user_id))
				$model->user_id = auth()->id();
		});

		static::Deleted(function ($model) {
			UpdateUserFavoriteSequencesCount::dispatch($model->user);

			if (!empty($model->sequence))
				$model->sequence->addedToFavoritesUsersCountRefresh();
		});

		static::Saved(function ($model) {
			UpdateUserFavoriteSequencesCount::dispatch($model->user);

			if (!empty($model->sequence))
				$model->sequence->addedToFavoritesUsersCountRefresh();
		});

		parent::boot();
	}

	public function user()
	{
		return $this->hasOne('App\User', 'id', 'user_id')
			->any();
	}

	public function sequence()
	{
		return $this->belongsTo('App\Sequence', 'sequence_id', 'id')
			->any();
	}
}
