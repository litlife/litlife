<?php

namespace App;

use App\Model as Model;
use App\Traits\UserCreate;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

/**
 * App\CommentVote
 *
 * @property int $comment_id
 * @property int $create_user_id
 * @property int $vote
 * @property string|null $ip
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $id
 * @property-read Comment $comment
 * @property-read User $create_user
 * @method static Builder|CommentVote newModelQuery()
 * @method static Builder|CommentVote newQuery()
 * @method static Builder|Model orderByField($column, $ids)
 * @method static Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
 * @method static Builder|CommentVote query()
 * @method static Builder|Model void()
 * @method static Builder|CommentVote whereCommentId($value)
 * @method static Builder|CommentVote whereCreateUserId($value)
 * @method static Builder|CommentVote whereCreatedAt($value)
 * @method static Builder|CommentVote whereCreator(User $user)
 * @method static Builder|CommentVote whereId($value)
 * @method static Builder|CommentVote whereIp($value)
 * @method static Builder|CommentVote whereUpdatedAt($value)
 * @method static Builder|CommentVote whereVote($value)
 * @mixin Eloquent
 */
class CommentVote extends Model
{
    use UserCreate;

    function comment()
    {
        return $this->belongsTo('App\Comment');
    }
}
