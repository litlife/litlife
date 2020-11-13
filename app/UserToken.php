<?php

namespace App;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Carbon;

/**
 * App\UserToken
 *
 * @property int $user_id
 * @property string $token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read \App\User $user
 * @method static Builder|UserToken newModelQuery()
 * @method static Builder|UserToken newQuery()
 * @method static Builder|UserToken query()
 * @method static Builder|UserToken whereCreatedAt($value)
 * @method static Builder|UserToken whereToken($value)
 * @method static Builder|UserToken whereUpdatedAt($value)
 * @method static Builder|UserToken whereUserId($value)
 * @mixin Eloquent
 */
class UserToken extends Authenticatable
{
    protected $guarded = ['user_id', 'token'];

    protected $primaryKey = 'user_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'token'
    ];

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
