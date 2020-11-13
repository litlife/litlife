<?php

namespace App;

use App\Model as Model;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

/**
 * App\UserSocialAccount
 *
 * @property int $id
 * @property int $user_id
 * @property string $provider_user_id
 * @property string $provider
 * @property string $access_token
 * @property object $parameters
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read \App\User $user
 * @method static Builder|UserSocialAccount newModelQuery()
 * @method static Builder|UserSocialAccount newQuery()
 * @method static Builder|Model orderByField($column, $ids)
 * @method static Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
 * @method static Builder|UserSocialAccount query()
 * @method static Builder|Model void()
 * @method static Builder|UserSocialAccount whereAccessToken($value)
 * @method static Builder|UserSocialAccount whereCreatedAt($value)
 * @method static Builder|UserSocialAccount whereId($value)
 * @method static Builder|UserSocialAccount whereParameters($value)
 * @method static Builder|UserSocialAccount whereProvider($value)
 * @method static Builder|UserSocialAccount whereProviderUserId($value)
 * @method static Builder|UserSocialAccount whereUpdatedAt($value)
 * @method static Builder|UserSocialAccount whereUserId($value)
 * @mixin Eloquent
 */
class UserSocialAccount extends Model
{
    protected $casts = [
        'parameters' => 'object'
    ];

    protected $fillable = [
        'provider_user_id',
        'provider',
        'access_token',
        'parameters'
    ];

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
