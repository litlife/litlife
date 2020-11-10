<?php

namespace App;

use App\Model as Model;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

/**
 * App\UserTopicSubscription
 *
 * @property int $id
 * @property int $topic_id
 * @property int $user_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Topic $topic
 * @property-read User $user
 * @method static Builder|UserTopicSubscription newModelQuery()
 * @method static Builder|UserTopicSubscription newQuery()
 * @method static Builder|Model orderByField($column, $ids)
 * @method static Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
 * @method static Builder|UserTopicSubscription query()
 * @method static Builder|Model void()
 * @method static Builder|UserTopicSubscription whereCreatedAt($value)
 * @method static Builder|UserTopicSubscription whereId($value)
 * @method static Builder|UserTopicSubscription whereTopicId($value)
 * @method static Builder|UserTopicSubscription whereUpdatedAt($value)
 * @method static Builder|UserTopicSubscription whereUserId($value)
 * @mixin Eloquent
 */
class UserTopicSubscription extends Model
{
    public $fillable = [
        'user_id',
        'topic_id'
    ];

    public function user()
    {
        return $this->belongsTo('App\User')->any();
    }

    public function topic()
    {
        return $this->belongsTo('App\Topic')->any();
    }

}
