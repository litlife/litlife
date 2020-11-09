<?php

namespace App;

use Awobaz\Compoships\Compoships;

/**
 * App\UserSearchSetting
 *
 * @property int $user_id
 * @property string $name Название настройки
 * @property string $value Значение настройки
 * @property-read \App\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|UserSearchSetting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserSearchSetting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByField($column, $ids)
 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
 * @method static \Illuminate\Database\Eloquent\Builder|UserSearchSetting query()
 * @method static \Illuminate\Database\Eloquent\Builder|Model void()
 * @method static \Illuminate\Database\Eloquent\Builder|UserSearchSetting whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserSearchSetting whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserSearchSetting whereValue($value)
 * @mixin \Eloquent
 */
class UserSearchSetting extends Model
{
	use Compoships;

	public $timestamps = false;

	public $incrementing = false;

	public $primaryKey = ['user_id', 'name'];

	public $fillable = [
		'name',
		'value'
	];

	public function user()
	{
		return $this->belongsTo('App\User', "user_id", "id");
	}
}
