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
use Illuminate\Support\Str;

/**
 * App\SupportQuestion
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
 * @property-read \App\SupportQuestionMessage|null $latest_message
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\SupportQuestionMessage[] $messages
 * @property-read \App\User|null $status_changed_user
 * @method static \Illuminate\Database\Eloquent\Builder|SupportQuestion accepted()
 * @method static \Illuminate\Database\Eloquent\Builder|SupportQuestion acceptedAndSentForReview()
 * @method static \Illuminate\Database\Eloquent\Builder|SupportQuestion acceptedAndSentForReviewOrBelongsToAuthUser()
 * @method static \Illuminate\Database\Eloquent\Builder|SupportQuestion acceptedAndSentForReviewOrBelongsToUser($user)
 * @method static \Illuminate\Database\Eloquent\Builder|SupportQuestion acceptedOrBelongsToAuthUser()
 * @method static \Illuminate\Database\Eloquent\Builder|SupportQuestion acceptedOrBelongsToUser($user)
 * @method static \Illuminate\Database\Eloquent\Builder|SupportQuestion checked()
 * @method static \Illuminate\Database\Eloquent\Builder|SupportQuestion checkedAndOnCheck()
 * @method static \Illuminate\Database\Eloquent\Builder|SupportQuestion checkedOrBelongsToUser($user)
 * @method static \Illuminate\Database\Eloquent\Builder|SupportQuestion newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SupportQuestion newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SupportQuestion onCheck()
 * @method static \Illuminate\Database\Eloquent\Builder|SupportQuestion onlyChecked()
 * @method static \Illuminate\Database\Query\Builder|SupportQuestion onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|SupportQuestion orderStatusChangedAsc()
 * @method static \Illuminate\Database\Eloquent\Builder|SupportQuestion orderStatusChangedDesc()
 * @method static \Illuminate\Database\Eloquent\Builder|SupportQuestion private ()
 * @method static \Illuminate\Database\Eloquent\Builder|SupportQuestion query()
 * @method static \Illuminate\Database\Eloquent\Builder|SupportQuestion sentOnReview()
 * @method static \Illuminate\Database\Eloquent\Builder|SupportQuestion unaccepted()
 * @method static \Illuminate\Database\Eloquent\Builder|SupportQuestion unchecked()
 * @method static \Illuminate\Database\Eloquent\Builder|SupportQuestion whereCreateUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SupportQuestion whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SupportQuestion whereCreator(\App\User $user)
 * @method static \Illuminate\Database\Eloquent\Builder|SupportQuestion whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SupportQuestion whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SupportQuestion whereLatestMessageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SupportQuestion whereNumberOfMessages($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SupportQuestion whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SupportQuestion whereStatusChangedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SupportQuestion whereStatusChangedUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SupportQuestion whereStatusIn($statuses)
 * @method static \Illuminate\Database\Eloquent\Builder|SupportQuestion whereStatusNot($status)
 * @method static \Illuminate\Database\Eloquent\Builder|SupportQuestion whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SupportQuestion whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|SupportQuestion withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|SupportQuestion withUnchecked()
 * @method static \Illuminate\Database\Eloquent\Builder|SupportQuestion withoutCheckedScope()
 * @method static \Illuminate\Database\Query\Builder|SupportQuestion withoutTrashed()
 * @mixin \Eloquent
 */
class SupportQuestion extends Model
{
	use UserCreate;
	use CheckedItems;
	use SoftDeletes;

	protected $attributes = [
		'status' => StatusEnum::OnReview
	];

	public $fillable = [
		'title',
		'category'
	];

	public function messages()
	{
		return $this->hasMany('App\SupportQuestionMessage');
	}

	public function latest_message()
	{
		return $this->hasOne('App\SupportQuestionMessage', 'id', 'latest_message_id');
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
			$query->whereColumn('support_questions.create_user_id', '=', 'support_question_messages.create_user_id');
		});
	}

	public function scopeWhereLastResponseNotByUser($query)
	{
		return $query->whereHas('latest_message', function (Builder $query) {
			$query->whereColumn('support_questions.create_user_id', '!=', 'support_question_messages.create_user_id');
		});
	}

	public function isLatestMessageByCreatedUser(): bool
	{
		return $this->create_user->is($this->latest_message->create_user);
	}

	static function getNumberInProcess(): int
	{
		return Cache::tags([CacheTags::NumberInProcessSupportQuestions])
			->remember(CacheTags::NumberInProcessSupportQuestions, 86400, function () {

				$count = SupportQuestion::whereStatusIn(['ReviewStarts'])
					->count();

				return (int)$count;
			});
	}

	static function flushNumberInProcess()
	{
		Cache::tags([CacheTags::NumberInProcessSupportQuestions])
			->pull(CacheTags::NumberInProcessSupportQuestions);
	}

	static function getNumberOfSolved(): int
	{
		return Cache::tags([CacheTags::NumberOfSolvedSupportQuestions])
			->remember(CacheTags::NumberOfSolvedSupportQuestions, 86400, function () {

				$count = SupportQuestion::accepted()
					->count();

				return (int)$count;
			});
	}

	static function flushNumberOfSolved()
	{
		Cache::tags([CacheTags::NumberOfSolvedSupportQuestions])
			->pull(CacheTags::NumberOfSolvedSupportQuestions);
	}

	static function getNumberOfNewQuestions(): int
	{
		return Cache::tags([CacheTags::NumberOfNewSupportQuestions])
			->remember(CacheTags::NumberOfNewSupportQuestions, 86400, function () {

				$count = SupportQuestion::whereStatusIn(['OnReview'])
					->count();

				return (int)$count;
			});
	}

	static function flushNumberOfNewQuestions()
	{
		Cache::tags([CacheTags::NumberOfNewSupportQuestions])
			->pull(CacheTags::NumberOfNewSupportQuestions);
	}

	public function setTitleAttribute($value)
	{
		$this->attributes['title'] = Str::limit($value, 97);
	}
}
