<?php

namespace App;

use App\Model as Model;

/**
 * App\UserReference
 *
 * @property-read \App\User $refer_user
 * @property-read \App\User $referred_user
 * @method static \Illuminate\Database\Eloquent\Builder|UserReference newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserReference newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByField($column, $ids)
 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
 * @method static \Illuminate\Database\Eloquent\Builder|UserReference query()
 * @method static \Illuminate\Database\Eloquent\Builder|Model void()
 * @mixin \Eloquent
 */
class UserReference extends Model
{
	public function refer_user()
	{
		return $this->belongsTo('App\User', 'refer_user_id', 'id');
	}

	public function referred_user()
	{
		return $this->belongsTo('App\User', 'referred_user_id', 'id');
	}
}
