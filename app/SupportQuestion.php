<?php

namespace App;

use App\Enums\CacheTags;
use App\Enums\StatusEnum;
use App\Traits\CheckedItems;
use App\Traits\UserCreate;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

/**
 * App\SupportQuestion
 *
 * @property int $id
 * @property int|null $category
 * @property string|null $title Заголовок
 * @property int $create_user_id Создатель запроса в поддержку
 * @property int|null $status
 * @property string|null $status_changed_at
 * @property int|null $status_changed_user_id
 * @property int|null $latest_message_id ID последнего сообщения
 * @property int $number_of_messages Количество сообщений
 * @property string|null $last_message_created_at Количество сообщений
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read User $create_user
 * @property-read FeedbackSupportResponses|null $feedback
 * @property-read mixed $is_accepted
 * @property-read mixed $is_private
 * @property-read mixed $is_rejected
 * @property-read mixed $is_review_starts
 * @property-read mixed $is_sent_for_review
 * @property-read SupportQuestionMessage|null $latest_message
 * @property-read \Illuminate\Database\Eloquent\Collection|SupportQuestionMessage[] $messages
 * @property-read User|null $status_changed_user
 * @method static Builder|SupportQuestion accepted()
 * @method static Builder|SupportQuestion acceptedAndSentForReview()
 * @method static Builder|SupportQuestion acceptedAndSentForReviewOrBelongsToAuthUser()
 * @method static Builder|SupportQuestion acceptedAndSentForReviewOrBelongsToUser($user)
 * @method static Builder|SupportQuestion acceptedOrBelongsToAuthUser()
 * @method static Builder|SupportQuestion acceptedOrBelongsToUser($user)
 * @method static Builder|SupportQuestion checked()
 * @method static Builder|SupportQuestion checkedAndOnCheck()
 * @method static Builder|SupportQuestion checkedOrBelongsToUser($user)
 * @method static Builder|SupportQuestion newModelQuery()
 * @method static Builder|SupportQuestion newQuery()
 * @method static Builder|SupportQuestion onCheck()
 * @method static Builder|SupportQuestion onlyChecked()
 * @method static \Illuminate\Database\Query\Builder|SupportQuestion onlyTrashed()
 * @method static Builder|SupportQuestion orderStatusChangedAsc()
 * @method static Builder|SupportQuestion orderStatusChangedDesc()
 * @method static Builder|SupportQuestion private ()
 * @method static Builder|SupportQuestion query()
 * @method static Builder|SupportQuestion sentOnReview()
 * @method static Builder|SupportQuestion unaccepted()
 * @method static Builder|SupportQuestion unchecked()
 * @method static Builder|SupportQuestion whereCategory($value)
 * @method static Builder|SupportQuestion whereCreateUserId($value)
 * @method static Builder|SupportQuestion whereCreatedAt($value)
 * @method static Builder|SupportQuestion whereCreator(User $user)
 * @method static Builder|SupportQuestion whereDeletedAt($value)
 * @method static Builder|SupportQuestion whereId($value)
 * @method static Builder|SupportQuestion whereLastMessageCreatedAt($value)
 * @method static Builder|SupportQuestion whereLastResponseByUser()
 * @method static Builder|SupportQuestion whereLastResponseNotByUser()
 * @method static Builder|SupportQuestion whereLatestMessageId($value)
 * @method static Builder|SupportQuestion whereNumberOfMessages($value)
 * @method static Builder|SupportQuestion whereStatus($value)
 * @method static Builder|SupportQuestion whereStatusChangedAt($value)
 * @method static Builder|SupportQuestion whereStatusChangedUserId($value)
 * @method static Builder|SupportQuestion whereStatusIn($statuses)
 * @method static Builder|SupportQuestion whereStatusNot($status)
 * @method static Builder|SupportQuestion whereTitle($value)
 * @method static Builder|SupportQuestion whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|SupportQuestion withTrashed()
 * @method static Builder|SupportQuestion withUnchecked()
 * @method static Builder|SupportQuestion withoutCheckedScope()
 * @method static \Illuminate\Database\Query\Builder|SupportQuestion withoutTrashed()
 * @mixin Eloquent
 */
class SupportQuestion extends Model
{
    use UserCreate;
    use CheckedItems;
    use SoftDeletes;

    public $fillable = [
        'title',
        'category'
    ];
    protected $attributes = [
        'status' => StatusEnum::OnReview
    ];

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

    public function latest_message()
    {
        return $this->hasOne('App\SupportQuestionMessage', 'id', 'latest_message_id');
    }

    public function upadateNumberOfMessages()
    {
        $this->number_of_messages = $this->messages()->count();
    }

    public function messages()
    {
        return $this->hasMany('App\SupportQuestionMessage');
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

    public function setTitleAttribute($value)
    {
        $this->attributes['title'] = Str::limit($value, 97);
    }

    public function feedback()
    {
        return $this->hasOne('App\FeedbackSupportResponses');
    }

    public function hasFeedback(): bool
    {
        if ($this->isAccepted()) {
            if ($this->feedback) {
                return true;
            }
        }

        return false;
    }
}
