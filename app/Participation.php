<?php

namespace App;

use App\Model as Model;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

/**
 * App\Participation
 *
 * @property int $user_id
 * @property int $conversation_id
 * @property int $new_messages_count
 * @property int|null $latest_seen_message_id
 * @property int|null $latest_message_id
 * @property Carbon|null $created_at
 * @property-read \App\Conversation $conversation
 * @property-read \App\Message|null $latest_message
 * @property-read \App\Message|null $latest_seen_message
 * @property-read \App\User $user
 * @method static Builder|Participation hasMessages()
 * @method static Builder|Participation newModelQuery()
 * @method static Builder|Participation newQuery()
 * @method static Builder|Model orderByField($column, $ids)
 * @method static Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
 * @method static Builder|Participation query()
 * @method static Builder|Model void()
 * @method static Builder|Participation whereConversationId($value)
 * @method static Builder|Participation whereCreatedAt($value)
 * @method static Builder|Participation whereLatestMessageId($value)
 * @method static Builder|Participation whereLatestSeenMessageId($value)
 * @method static Builder|Participation whereNewMessagesCount($value)
 * @method static Builder|Participation whereUserId($value)
 * @mixin Eloquent
 */
class Participation extends Model
{
    const UPDATED_AT = null;

    public $incrementing = false;

    protected $primaryKey = [
        'user_id',
        'conversation_id'
    ];

    protected $casts = [
        'new_messages_count' => 'integer'
    ];

    function latest_seen_message()
    {
        return $this->hasOne('App\Message', 'id', 'latest_seen_message_id');
    }

    public function conversation()
    {
        return $this->belongsTo('App\Conversation');
    }

    public function user()
    {
        return $this->belongsTo('App\User')
            ->any();
    }

    function latest_message()
    {
        return $this->hasOne('App\Message', 'id', 'latest_message_id');
    }

    function scopeHasMessages($query)
    {
        return $query->whereNotNull('latest_message_id');
    }

    public function updateLatestMessage()
    {
        $latestMessage = $this->getLatestMessage();

        if (!empty($latestMessage)) {
            $this->latest_message_id = $latestMessage->id;
        } else {
            $this->latest_message_id = null;
        }
    }

    public function getLatestMessage()
    {
        return $this->conversation
            ->messages()
            ->notDeletedForUser($this->user_id)
            ->latestWithId()
            ->limit(1)
            ->first();
    }

    public function updateNewMessagesCount()
    {
        $this->new_messages_count = $this->getNewMessagesCount();
    }

    public function getNewMessagesCount(): int
    {
        return $this->conversation
            ->messages()
            ->notDeletedForUser($this->user_id)
            ->where('id', '>', $this->latest_seen_message_id ?? 0)
            ->count();
    }

    public function noNewMessages()
    {
        $this->latest_seen_message_id = $this->latest_message_id;
        $this->new_messages_count = 0;
    }

    public function hasNewMessages(): bool
    {
        return $this->new_messages_count > 0;
    }

    public function getNewMessagesCountAttribute($value)
    {
        if ($value < 0) {
            $value = 0;
        }

        return intval($value);
    }
}
