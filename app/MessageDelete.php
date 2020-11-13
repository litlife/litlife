<?php

namespace App;

use App\Model as Model;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;

/**
 * App\MessageDelete
 *
 * @property int $message_id
 * @property int $user_id
 * @property string $deleted_at
 * @property-read \App\Message $message
 * @property-read \App\User $user
 * @method static Builder|MessageDelete newModelQuery()
 * @method static Builder|MessageDelete newQuery()
 * @method static Builder|Model orderByField($column, $ids)
 * @method static Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
 * @method static Builder|MessageDelete query()
 * @method static Builder|Model void()
 * @method static Builder|MessageDelete whereDeletedAt($value)
 * @method static Builder|MessageDelete whereMessageId($value)
 * @method static Builder|MessageDelete whereUserId($value)
 * @mixin Eloquent
 */
class MessageDelete extends Model
{
    const UPDATED_AT = null;
    const CREATED_AT = null;

    public $table = 'message_deletions';
    public $fillable = [
        'user_id',
        'message_id',
        'deleted_at'
    ];
    public $incrementing = false;
    protected $primaryKey = [
        'user_id',
        'message_id'
    ];

    function message()
    {
        return $this->belongsTo('App\Message');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }


}
