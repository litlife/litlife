<?php

namespace App;

use App\Model as Model;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;

/**
 * App\UserReference
 *
 * @property-read \App\User $refer_user
 * @property-read \App\User $referred_user
 * @method static Builder|UserReference newModelQuery()
 * @method static Builder|UserReference newQuery()
 * @method static Builder|Model orderByField($column, $ids)
 * @method static Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
 * @method static Builder|UserReference query()
 * @method static Builder|Model void()
 * @mixin Eloquent
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
