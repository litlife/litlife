<?php

namespace App\Traits;

use App\Enums\UserRelationType;

trait Friendship
{
	public $relation_to_users = [];

	public function isNobodyTo($user)
	{
		$relation = $this->relationship
			->where('user_id2', $user->id)
			->first();

		if (is_null($relation) or $relation->status == UserRelationType::Null) {
			if (!$this->isSubscriptionOf($user) and !$this->addedToBlacklistBy($user))
				return true;
		}

		return false;
	}

	public function isSubscriptionOf($user)
	{
		$relation = $this->relationshipReverse
			->where('user_id', $user->id)
			->first();

		if (optional($relation)->status == UserRelationType::Subscriber)
			return true;

		return false;
	}

	public function addedToBlacklistBy($user)
	{
		$relation = $this->relationshipReverse
			->where('user_id', $user->id)
			->first();

		if (optional($relation)->status == UserRelationType::Blacklist)
			return true;

		return false;
	}

	public function isFriendOf($user)
	{
		$relation = $this->relationship
			->where('user_id2', $user->id)
			->first();

		if (optional($relation)->status == UserRelationType::Friend)
			return true;

		return false;
	}

	public function isSubscriberOf($user)
	{
		$relation = $this->relationship
			->where('user_id2', $user->id)
			->first();

		if (optional($relation)->status == UserRelationType::Subscriber)
			return true;

		return false;
	}

	public function hasAddedToBlacklist($user)
	{
		$relation = $this->relationship
			->where('user_id2', $user->id)
			->first();

		if (optional($relation)->status == UserRelationType::Blacklist)
			return true;

		return false;
	}

	public function relationship()
	{
		return $this->hasMany('App\UserRelation', 'user_id', 'id');
	}

	public function friends()
	{
		return $this->belongsToMany('App\User', 'user_relations', 'user_id', 'user_id2')
			->withPivot('status', 'user_updated_at')
			->wherePivot('status', UserRelationType::Friend);
	}

	// друзья

	public function subscriptions()
	{
		return $this->belongsToMany('App\User', 'user_relations', 'user_id', 'user_id2')
			->withPivot('status', 'user_updated_at')
			->wherePivot('status', UserRelationType::Subscriber);
	}

	// подписки

	public function subscribers()
	{
		return $this->belongsToMany('App\User', 'user_relations', 'user_id2', 'user_id')
			->withPivot('status', 'user_updated_at')
			->wherePivot('status', UserRelationType::Subscriber);
	}

	// подписчики

	public function blacklists()
	{
		return $this->belongsToMany('App\User', 'user_relations', 'user_id', 'user_id2')
			->withPivot('status', 'user_updated_at')
			->wherePivot('status', UserRelationType::Blacklist);
	}

	// Черный список

	public function friendsAndSubscriptions()
	{
		return $this->relationshipReverse()
			->whereIn('user_relations.status', [UserRelationType::Subscriber, UserRelationType::Friend]);
	}

	public function relationshipReverse()
	{
		return $this->hasMany('App\UserRelation', 'user_id2', 'id');
	}

	public function relationOnUser($user)
	{
		if (empty($this->relation_to_users[$user->id])) {

			$this->relation_to_users[$user->id] = $this->relationship
				->where('user_id2', $user->id)
				->first();
		}
		return $this->relation_to_users[$user->id];
	}

	public function relation_to_user($user)
	{
		if (empty($this->relation_to_users[$user->id])) {

			$this->relation_to_users[$user->id] = $this->relationship
				->where('user_id2', $user->id)
				->first();
		}
		return $this->relation_to_users[$user->id];
	}
}