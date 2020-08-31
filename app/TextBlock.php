<?php

namespace App;

use App\Model as Model;
use App\Traits\UserCreate;
use Auth;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

/**
 * App\TextBlock
 *
 * @property string $name
 * @property string $text
 * @property int $user_id
 * @property int|null $time
 * @property int $show_for_all
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $id
 * @property string|null $user_edited_at Время когда пользователь отредактировал
 * @property-read \App\User $create_user
 * @method static Builder|TextBlock name($name)
 * @method static Builder|TextBlock newModelQuery()
 * @method static Builder|TextBlock newQuery()
 * @method static Builder|Model orderByField($column, $ids)
 * @method static Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
 * @method static Builder|TextBlock query()
 * @method static Builder|Model void()
 * @method static Builder|TextBlock whereCreatedAt($value)
 * @method static Builder|TextBlock whereCreator(\App\User $user)
 * @method static Builder|TextBlock whereId($value)
 * @method static Builder|TextBlock whereName($value)
 * @method static Builder|TextBlock whereShowForAll($value)
 * @method static Builder|TextBlock whereText($value)
 * @method static Builder|TextBlock whereTime($value)
 * @method static Builder|TextBlock whereUpdatedAt($value)
 * @method static Builder|TextBlock whereUserEditedAt($value)
 * @method static Builder|TextBlock whereUserId($value)
 * @mixin Eloquent
 */
class TextBlock extends Model
{
	use UserCreate;

	protected $fillable = [
		'text',
		'show_for_all',
		'name'
	];

	public static function boot()
	{
		static::Creating(function ($model) {
			if (empty($model->user_id))
				$model->user_id = Auth::id();
		});

		static::Updating(function ($model) {
			if (auth()->check())
				$model->user_id = Auth::id();
		});

		parent::boot();
	}

	static public function latestVersion($name)
	{
		return self::name($name)
			->orderBy('created_at', 'desc')
			->first();
	}

	public function scopeName($query, $name)
	{
		return $query->where('name', $name);
	}

	public function getCreateUserIdColumn()
	{
		return 'user_id';
	}
}
