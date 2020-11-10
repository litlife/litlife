<?php

namespace App;

use App\Model as Model;
use App\Traits\UserAgentTrait;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

/**
 * App\UserAuthLog
 *
 * @property int $id
 * @property int $user_id
 * @property string $ip
 * @property int $time
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int|null $user_agent_id
 * @property bool|null $is_remember_me_enabled
 * @property-read User $user
 * @property-read UserAgent|null $user_agent
 * @method static Builder|UserAuthLog newModelQuery()
 * @method static Builder|UserAuthLog newQuery()
 * @method static Builder|Model orderByField($column, $ids)
 * @method static Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
 * @method static Builder|UserAuthLog query()
 * @method static Builder|Model void()
 * @method static Builder|UserAuthLog whereCreatedAt($value)
 * @method static Builder|UserAuthLog whereId($value)
 * @method static Builder|UserAuthLog whereIp($value)
 * @method static Builder|UserAuthLog whereIsRememberMeEnabled($value)
 * @method static Builder|UserAuthLog whereTime($value)
 * @method static Builder|UserAuthLog whereUpdatedAt($value)
 * @method static Builder|UserAuthLog whereUserAgentId($value)
 * @method static Builder|UserAuthLog whereUserId($value)
 * @mixin Eloquent
 */
class UserAuthLog extends Model
{
    use UserAgentTrait;

    protected $dates = [
        'created_at',
        'updated_at'
    ];

    public static function boot()
    {
        static::Creating(function ($model) {
            $model->user_agent_id = UserAgent::getCurrentId();
        });
        parent::boot();
    }

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id', 'id');
    }
}
