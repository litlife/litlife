<?php

namespace App;

use App\Enums\UserRelationType;
use App\Model as Model;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;


/**
 * App\UserRelation
 *
 * @property int $user_id
 * @property int $user_id2
 * @property int|null $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $id
 * @property string $user_updated_at
 * @property-read User|null $first_user
 * @property-read User|null $second_user
 * @method static Builder|UserRelation friends()
 * @method static Builder|UserRelation friendsAndSubscribers()
 * @method static Builder|UserRelation newModelQuery()
 * @method static Builder|UserRelation newQuery()
 * @method static Builder|Model orderByField($column, $ids)
 * @method static Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
 * @method static Builder|UserRelation query()
 * @method static Builder|Model void()
 * @method static Builder|UserRelation whereCreatedAt($value)
 * @method static Builder|UserRelation whereId($value)
 * @method static Builder|UserRelation whereStatus($value)
 * @method static Builder|UserRelation whereUpdatedAt($value)
 * @method static Builder|UserRelation whereUserId($value)
 * @method static Builder|UserRelation whereUserId2($value)
 * @method static Builder|UserRelation whereUserUpdatedAt($value)
 * @mixin Eloquent
 */
class UserRelation extends Model
{
    public $incrementing = true;
    protected $fillable = [
        'user_id',
        'user_id2',
        'status',
        'user_updated_at'
    ];

    public function first_user()
    {
        return $this->hasOne('App\User', 'id', 'user_id');
    }

    public function second_user()
    {
        return $this->hasOne('App\User', 'id', 'user_id2');
    }

    public function scopeFriends($query)
    {
        return $query->where('status', UserRelationType::Friend);
    }

    public function scopeFriendsAndSubscribers($query)
    {
        return $query->whereIn('status', [UserRelationType::Subscriber, UserRelationType::Friend]);
    }
}
