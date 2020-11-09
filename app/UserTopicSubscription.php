<?php

namespace App;

use App\Model as Model;

/**
 * App\UserTopicSubscription
 *
 * @property int $id
 * @property int $topic_id
 * @property int $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Topic $topic
 * @property-read \App\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|UserTopicSubscription newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserTopicSubscription newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByField($column, $ids)
 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
 * @method static \Illuminate\Database\Eloquent\Builder|UserTopicSubscription query()
 * @method static \Illuminate\Database\Eloquent\Builder|Model void()
 * @method static \Illuminate\Database\Eloquent\Builder|UserTopicSubscription whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserTopicSubscription whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserTopicSubscription whereTopicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserTopicSubscription whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserTopicSubscription whereUserId($value)
 * @mixin \Eloquent
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
