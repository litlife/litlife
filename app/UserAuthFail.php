<?php

namespace App;

use App\Model as Model;
use App\Traits\UserAgentTrait;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

/**
 * App\UserAuthFail
 *
 * @property int $id
 * @property int $user_id
 * @property string|null $password
 * @property string $ip
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int|null $user_agent_id
 * @property-read \App\UserAgent|null $user_agent
 * @method static Builder|UserAuthFail newModelQuery()
 * @method static Builder|UserAuthFail newQuery()
 * @method static Builder|Model orderByField($column, $ids)
 * @method static Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
 * @method static Builder|UserAuthFail query()
 * @method static Builder|Model void()
 * @method static Builder|UserAuthFail whereCreatedAt($value)
 * @method static Builder|UserAuthFail whereId($value)
 * @method static Builder|UserAuthFail whereIp($value)
 * @method static Builder|UserAuthFail wherePassword($value)
 * @method static Builder|UserAuthFail whereUpdatedAt($value)
 * @method static Builder|UserAuthFail whereUserAgentId($value)
 * @method static Builder|UserAuthFail whereUserId($value)
 * @mixin Eloquent
 */
class UserAuthFail extends Model
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

    public function getPasswordAttribute()
    {
        $password = $this->attributes['password'];

        $password = substr_replace($password, '**', 1, 2);
        $password = substr_replace($password, '**', 4, 2);
        $password = substr_replace($password, '**', 7, 2);
        $password = substr_replace($password, '**', 10, 2);

        return $password;
    }
}
