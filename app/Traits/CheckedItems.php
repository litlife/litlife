<?php

namespace App\Traits;

use App\Enums\StatusEnum;
use App\Scopes\CheckedScope;
use App\User;
use Illuminate\Support\Facades\Auth;

trait CheckedItems
{
	// checked_at           accepted_at
	// added_for_check_at   sent_for_review_at
	// decline_at           rejected_at

	/*
	status
	status_changed_at
	status_changed_user_id
	 */

	public function status_changed_user()
	{
		return $this->hasOne('App\User', 'id', 'status_changed_user_id');
	}

	public function getIsRejectedAttribute()
	{
		return $this->isRejected();
	}

	public function isRejected()
	{
		return $this->isStatus('Rejected');
	}

	public function isStatus($status)
	{
		return (StatusEnum::getValue($status) == $this->{$this->getStatusColumn()});
	}

	public function getStatusColumn()
	{
		return 'status';
	}

	public function getIsPrivateAttribute()
	{
		return $this->isPrivate();
	}

	public function isPrivate()
	{
		return $this->isStatus('Private');
	}

	public function getIsSentForReviewAttribute()
	{
		return $this->isSentForReview();
	}

	public function isSentForReview()
	{
		return $this->isStatus('OnReview');
	}

	public function getIsAcceptedAttribute()
	{
		return $this->isAccepted();
	}

	public function isAccepted()
	{
		return $this->isStatus('Accepted');
	}

	public function getIsReviewStartsAttribute()
	{
		return $this->isReviewStarts();
	}

	public function isReviewStarts()
	{
		return $this->isStatus('ReviewStarts');
	}

	public function isStatusChanged()
	{
		return $this->isChanged($this->getStatusColumn());
	}

	public function statusAccepted()
	{
		$this->changeStatus('Accepted');
	}

	public function changeStatus($status)
	{
		if ($this->{$this->getStatusColumn()} != StatusEnum::getValue($status)) {
			$this->{$this->getStatusColumn()} = StatusEnum::getValue($status);
			$this->{$this->getStatusChangedAtColumn()} = now();
			$this->{$this->getStatusChangedUserIdColumn()} = Auth::id();
		}
	}

	public function getStatusChangedAtColumn()
	{
		return 'status_changed_at';
	}

	public function getStatusChangedUserIdColumn()
	{
		return 'status_changed_user_id';
	}

	public function statusSentForReview()
	{
		$this->changeStatus('OnReview');
	}

	public function statusReject()
	{
		$this->changeStatus('Rejected');
	}

	public function statusPrivate()
	{
		$this->changeStatus('Private');
	}

	public function statusReviewStarts()
	{
		$this->changeStatus('ReviewStarts');
	}

	public function scopeWhereStatus($query, $status)
	{
		return $query->where($this->getTable() . '.' . $this->getStatusColumn(), StatusEnum::getValue($status))
			->withoutGlobalScope(CheckedScope::class);
	}

	public function scopeWhereStatusIn($query, $statuses)
	{
		$array = [];

		foreach ($statuses as $key) {
			$array[] = StatusEnum::getValue($key);
		}

		return $query->whereIn($this->getTable() . '.' . $this->getStatusColumn(), $array)
			->withoutGlobalScope(CheckedScope::class);
	}

	public function scopeWhereStatusNot($query, $status)
	{
		return $query->where($this->getTable() . '.' . $this->getStatusColumn(), '!=', StatusEnum::getValue($status))
			->withoutGlobalScope(CheckedScope::class);
	}

	public function scopeSentOnReview($query)
	{
		// scopeOnCheck
		return $query->WhereStatusIn(['OnReview', 'ReviewStarts'])
			->withoutGlobalScope(CheckedScope::class);
	}

	// показывает любые проверенные

	public function scopePrivate($query)
	{
		// scopeOnCheck
		return $query->whereStatus('Private')
			->withoutGlobalScope(CheckedScope::class);
	}

	// показывает которые отправлены на проверку

	public function scopeWithoutCheckedScope($query)
	{
		return $query->withoutGlobalScope(CheckedScope::class);
	}

	// показывает которые отправлены на проверку

	public function scopeWithUnchecked($query)
	{
		return $query->withoutGlobalScope(CheckedScope::class);
	}

	// показывает любые не проверенные и отправленные и не отправленные на проверку

	public function scopeAcceptedOrBelongsToAuthUser($query)
	{
		if (auth()->check()) {
			return $query->acceptedOrBelongsToUser(auth()->user());
		} else
			return $query->accepted();
	}

	// показывает любые не проверенные или которые были отправлены на проверку и были проверенны

	public function scopeOrderStatusChangedAsc($query)
	{
		return $query->orderByRaw('"' . $this->getTable() . '"."' . $this->getStatusChangedAtColumn() . '" asc NULLS FIRST');
	}

	public function scopeOrderStatusChangedDesc($query)
	{
		return $query->orderByRaw('"' . $this->getTable() . '"."' . $this->getStatusChangedAtColumn() . '" desc NULLS LAST');
	}

	public function scopeCheckedOrBelongsToUser($query, $user)
	{
		return $this->scopeAcceptedOrBelongsToUser($query, $user);
	}

	public function scopeAcceptedOrBelongsToUser($query, $user)
	{
		if (isset($user)) {
			return $query->accepted()
				->orWhere($this->getTable() . '.create_user_id', $user->id);
		} else {
			return $query->accepted();
		}
	}

	public function scopeChecked($query)
	{
		return $this->scopeAccepted($query);
	}

	public function scopeAccepted($query)
	{
		// scopeChecked
		return $query->whereStatus('Accepted')
			->withoutGlobalScope(CheckedScope::class);
	}

	public function scopeOnlyChecked($query)
	{
		return $this->scopeAccepted($query);
	}


	// aliases

	public function scopeOnCheck($query)
	{
		return $this->sentOnReview($query);
	}

	// показывает любые проверенные

	public function scopeUnchecked($query)
	{
		return $this->scopeUnaccepted($query);
	}

	public function scopeUnaccepted($query)
	{
		// scopeUnchecked
		return $query->whereStatusNot('Accepted')->withoutGlobalScope(CheckedScope::class);
	}

	// показывает которые отправлены на проверку

	public function scopeCheckedAndOnCheck($query)
	{
		return $this->scopeAcceptedAndSentForReview($query);
	}

	// показывает любые не проверенные и отправленные и не отправленные на проверку

	public function scopeAcceptedAndSentForReview($query)
	{
		// scopeCheckedAndOnCheck
		return $query->whereStatusIn(['Accepted', 'OnReview', 'ReviewStarts'])->withoutGlobalScope(CheckedScope::class);
	}

	// показывает любые не проверенные или которые были отправлены на проверку и были проверенны

	public function scopeAcceptedAndSentForReviewOrBelongsToUser($query, $user)
	{
		$query->withoutGlobalScope(CheckedScope::class);

		if (isset($user)) {
			return $query->whereStatusIn(['Accepted', 'OnReview', 'ReviewStarts'])
				->orWhere($this->getTable() . '.create_user_id', $user->id);
		} else {
			return $query->whereStatusIn(['Accepted', 'OnReview', 'ReviewStarts']);
		}
	}

	public function scopeAcceptedAndSentForReviewOrBelongsToAuthUser($query)
	{
		if (auth()->check()) {
			return $query->acceptedAndSentForReviewOrBelongsToUser(auth()->user());
		} else {
			return $query->acceptedAndSentForReview();
		}
	}

	public function isUserChangedStatus(User $user = null)
	{
		$status_changed_user = $this->status_changed_user;

		if (empty($status_changed_user))
			return false;

		if (empty($user->id))
			return false;

		if ($user->id == $status_changed_user->id)
			return true;
		else
			return false;
	}

	protected function getArrayableAppends()
	{
		$this->appends = array_unique(array_merge($this->appends, [
			'is_private',
			'is_rejected',
			'is_sent_for_review',
			'is_accepted',
			'is_review_starts'
		]));

		return parent::getArrayableAppends();
	}
}