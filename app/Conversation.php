<?php

namespace App;

use App\Model as Model;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;

/**
 * App\Conversation
 *
 * @property int $id
 * @property int $latest_message_id
 * @property int $messages_count
 * @property int $participations_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Message[] $messages
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Participation[] $participations
 * @method static Builder|Conversation newModelQuery()
 * @method static Builder|Conversation newQuery()
 * @method static Builder|Model orderByField($column, $ids)
 * @method static Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
 * @method static Builder|Conversation query()
 * @method static Builder|Model void()
 * @method static Builder|Conversation whereId($value)
 * @method static Builder|Conversation whereLatestMessageId($value)
 * @method static Builder|Conversation whereMessagesCount($value)
 * @method static Builder|Conversation whereParticipationsCount($value)
 * @method static Builder|Conversation whereUsers($user, $user2)
 * @mixin Eloquent
 */
class Conversation extends Model
{
	const UPDATED_AT = null;
	const CREATED_AT = null;

	function messages()
	{
		return $this->hasMany('App\Message')
			->with('conversation.participations');
	}

	function participations()
	{
		return $this->hasMany('App\Participation');
	}

	public function scopeWhereUsers($query, $user, $user2)
	{
		if (is_object($user))
			$user = $user->getKey();

		if (is_object($user2))
			$user2 = $user2->getKey();

		return $query
			->whereHas('participations', function ($query) use ($user) {
				$query->where('user_id', $user);
			})->whereHas('participations', function ($query) use ($user2) {
				$query->where('user_id', $user2);
			})
			->with('participations');

		/*
		return $query->join('participations as p1', 'conversations.id', '=', 'p1.conversation_id')
			->join('participations as p2', 'conversations.id', '=', 'p2.conversation_id')
			->where(function ($query) use ($user, $user2) {
				$query->where('p1.user_id', $user)
					->where('p2.user_id', $user2);
			})
			->orWhere(function ($query) use ($user, $user2)  {
				$query->where('p2.user_id', $user)
					->where('p1.user_id', $user2);
			});
		*/
	}
}
