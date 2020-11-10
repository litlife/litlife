<?php

namespace App;

use Awobaz\Compoships\Compoships;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;

/**
 * App\UserSearchSetting
 *
 * @property int $user_id
 * @property string $name Название настройки
 * @property string $value Значение настройки
 * @property-read User $user
 * @method static Builder|UserSearchSetting newModelQuery()
 * @method static Builder|UserSearchSetting newQuery()
 * @method static Builder|Model orderByField($column, $ids)
 * @method static Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
 * @method static Builder|UserSearchSetting query()
 * @method static Builder|Model void()
 * @method static Builder|UserSearchSetting whereName($value)
 * @method static Builder|UserSearchSetting whereUserId($value)
 * @method static Builder|UserSearchSetting whereValue($value)
 * @mixin Eloquent
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
