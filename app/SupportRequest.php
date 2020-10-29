<?php

namespace App;

use App\Enums\CacheTags;
use App\Enums\StatusEnum;
use App\Traits\CheckedItems;
use App\Traits\UserCreate;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;

/**
 * App\SupportRequest
 *
 * @property int $id
 * @property string|null $title Заголовок
 * @property int $create_user_id Создатель сообщения
 * @property int|null $status
 * @property string|null $status_changed_at
 * @property int|null $status_changed_user_id
 * @property int|null $latest_message_id ID последнего сообщения
 * @property int $number_of_messages Количество сообщений
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\User $create_user
 * @property-read mixed $is_accepted
 * @property-read mixed $is_private
 * @property-read mixed $is_rejected
 * @property-read mixed $is_review_starts
 * @property-read mixed $is_sent_for_review
 * @property-read \App\SupportRequestMessage|null $latest_message
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\SupportRequestMessage[] $messages
 * @property-read \App\User|null $status_changed_user
 * @method static \Illuminate\Database\Eloquent\Builder|SupportRequest accepted()
 * @method static \Illuminate\Database\Eloquent\Builder|SupportRequest acceptedAndSentForReview()
 * @method static \Illuminate\Database\Eloquent\Builder|SupportRequest acceptedAndSentForReviewOrBelongsToAuthUser()
 * @method static \Illuminate\Database\Eloquent\Builder|SupportRequest acceptedAndSentForReviewOrBelongsToUser($user)
 * @method static \Illuminate\Database\Eloquent\Builder|SupportRequest acceptedOrBelongsToAuthUser()
 * @method static \Illuminate\Database\Eloquent\Builder|SupportRequest acceptedOrBelongsToUser($user)
 * @method static \Illuminate\Database\Eloquent\Builder|SupportRequest checked()
 * @method static \Illuminate\Database\Eloquent\Builder|SupportRequest checkedAndOnCheck()
 * @method static \Illuminate\Database\Eloquent\Builder|SupportRequest checkedOrBelongsToUser($user)
 * @method static \Illuminate\Database\Eloquent\Builder|SupportRequest newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SupportRequest newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SupportRequest onCheck()
 * @method static \Illuminate\Database\Eloquent\Builder|SupportRequest onlyChecked()
 * @method static \Illuminate\Database\Query\Builder|SupportRequest onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|SupportRequest orderStatusChangedAsc()
 * @method static \Illuminate\Database\Eloquent\Builder|SupportRequest orderStatusChangedDesc()
 * @method static \Illuminate\Database\Eloquent\Builder|SupportRequest private ()
 * @method static \Illuminate\Database\Eloquent\Builder|SupportRequest query()
 * @method static \Illuminate\Database\Eloquent\Builder|SupportRequest sentOnReview()
 * @method static \Illuminate\Database\Eloquent\Builder|SupportRequest unaccepted()
 * @method static \Illuminate\Database\Eloquent\Builder|SupportRequest unchecked()
 * @method static \Illuminate\Database\Eloquent\Builder|SupportRequest whereCreateUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SupportRequest whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SupportRequest whereCreator(\App\User $user)
 * @method static \Illuminate\Database\Eloquent\Builder|SupportRequest whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SupportRequest whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SupportRequest whereLatestMessageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SupportRequest whereNumberOfMessages($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SupportRequest whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SupportRequest whereStatusChangedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SupportRequest whereStatusChangedUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SupportRequest whereStatusIn($statuses)
 * @method static \Illuminate\Database\Eloquent\Builder|SupportRequest whereStatusNot($status)
 * @method static \Illuminate\Database\Eloquent\Builder|SupportRequest whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SupportRequest whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|SupportRequest withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|SupportRequest withUnchecked()
 * @method static \Illuminate\Database\Eloquent\Builder|SupportRequest withoutCheckedScope()
 * @method static \Illuminate\Database\Query\Builder|SupportRequest withoutTrashed()
 * @mixin \Eloquent
 */
class SupportRequest extends Model
{
	use UserCreate;
	use CheckedItems;
	use SoftDeletes;

	protected $attributes = [
		'status' => StatusEnum::OnReview
	];

	public $fillable = [
		'title'
	];

	public function messages()
	{
		return $this->hasMany('App\SupportRequestMessage');
	}

	public function latest_message()
	{
		return $this->hasOne('App\SupportRequestMessage', 'id', 'latest_message_id');
	}

	public function upadateNumberOfMessages()
	{
		$this->number_of_messages = $this->messages()->count();
	}

	public function upadateLatestMessage()
	{
		$latestMessage = $this->messages()
			->orderBy('id', 'desc')
			->first();

		if (!empty($latestMessage)) {
			$this->latest_message_id = $latestMessage->id;
			$this->last_message_created_at = $latestMessage->created_at;
		}
	}

	public function scopeWhereLastResponseByUser($query)
	{
		return $query->whereHas('latest_message', function (Builder $query) {
			$query->whereColumn('support_requests.create_user_id', '=', 'support_request_messages.create_user_id');
		});
	}

	public function scopeWhereLastResponseNotByUser($query)
	{
		return $query->whereHas('latest_message', function (Builder $query) {
			$query->whereColumn('support_requests.create_user_id', '!=', 'support_request_messages.create_user_id');
		});
	}

	public function isLatestMessageByCreatedUser(): bool
	{
		return $this->create_user->is($this->latest_message->create_user);
	}

	static function getNumberInProcess(): int
	{
		return Cache::tags([CacheTags::NumberInProcessSupportRequests])
			->remember(CacheTags::NumberInProcessSupportRequests, 86400, function () {

				$count = SupportRequest::whereStatusIn(['ReviewStarts'])
					->count();

				return (int)$count;
			});
	}

	static function flushNumberInProcess()
	{
		Cache::tags([CacheTags::NumberInProcessSupportRequests])
			->pull(CacheTags::NumberInProcessSupportRequests);
	}

	static function getNumberOfSolved(): int
	{
		return Cache::tags([CacheTags::NumberOfSolvedSupportRequests])
			->remember(CacheTags::NumberOfSolvedSupportRequests, 86400, function () {

				$count = SupportRequest::accepted()
					->count();

				return (int)$count;
			});
	}

	static function flushNumberOfSolved()
	{
		Cache::tags([CacheTags::NumberOfSolvedSupportRequests])
			->pull(CacheTags::NumberOfSolvedSupportRequests);
	}

	static function getNumberOfUnsolved(): int
	{
		return Cache::tags([CacheTags::NumberOfUnsolvedRequests])
			->remember(CacheTags::NumberOfUnsolvedRequests, 86400, function () {

				$count = SupportRequest::whereStatusIn(['OnReview'])
					->count();

				return (int)$count;
			});
	}

	static function flushNumberOfUnsolved()
	{
		Cache::tags([CacheTags::NumberOfUnsolvedRequests])
			->pull(CacheTags::NumberOfUnsolvedRequests);
	}
}
