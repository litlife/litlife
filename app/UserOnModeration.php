<?php

namespace App;

use App\Enums\CacheTags;
use App\Model as Model;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;


/**
 * App\UserOnModeration
 *
 * @property int $user_id
 * @property int|null $time
 * @property int $user_adds_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $id
 * @property-read \App\User|null $user
 * @property-read \App\User|null $user_adds
 * @method static Builder|UserOnModeration newModelQuery()
 * @method static Builder|UserOnModeration newQuery()
 * @method static Builder|Model orderByField($column, $ids)
 * @method static Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
 * @method static Builder|UserOnModeration query()
 * @method static Builder|Model void()
 * @method static Builder|UserOnModeration whereCreatedAt($value)
 * @method static Builder|UserOnModeration whereId($value)
 * @method static Builder|UserOnModeration whereTime($value)
 * @method static Builder|UserOnModeration whereUpdatedAt($value)
 * @method static Builder|UserOnModeration whereUserAddsId($value)
 * @method static Builder|UserOnModeration whereUserId($value)
 * @mixin Eloquent
 */
class UserOnModeration extends Model
{

	protected $table = 'users_on_moderation';

	static function getCachedCount()
	{
		return Cache::tags([CacheTags::UsersOnModerationCount])->remember('count', 3600, function () {
			return self::count();
		});
	}

	static function flushCachedCount()
	{
		Cache::tags([CacheTags::UsersOnModerationCount])->pull('count');
	}

	function user()
	{
		return $this->hasOne('App\User', 'id', 'user_id');
	}

	function user_adds()
	{
		return $this->hasOne('App\User', 'id', 'user_adds_id');
	}
}
