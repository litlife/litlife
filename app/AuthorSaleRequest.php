<?php

namespace App;

use App\Enums\CacheTags;
use App\Model as Model;
use App\Traits\CheckedItems;
use App\Traits\UserCreate;
use Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

/**
 * App\AuthorSaleRequest
 *
 * @property int $id
 * @property int $create_user_id
 * @property int $manager_id
 * @property int $author_id
 * @property string $text
 * @property string|null $review_comment
 * @property int|null $status
 * @property Carbon|null $status_changed_at
 * @property int|null $status_changed_user_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read \App\Author $author
 * @property-read \App\User $create_user
 * @property-read mixed $is_accepted
 * @property-read mixed $is_private
 * @property-read mixed $is_rejected
 * @property-read mixed $is_review_starts
 * @property-read mixed $is_sent_for_review
 * @property-read \App\Manager $manager
 * @property-read \App\User|null $status_changed_user
 * @method static \Illuminate\Database\Eloquent\Builder|AuthorSaleRequest accepted()
 * @method static \Illuminate\Database\Eloquent\Builder|AuthorSaleRequest acceptedAndSentForReview()
 * @method static \Illuminate\Database\Eloquent\Builder|AuthorSaleRequest acceptedAndSentForReviewOrBelongsToAuthUser()
 * @method static \Illuminate\Database\Eloquent\Builder|AuthorSaleRequest acceptedAndSentForReviewOrBelongsToUser($user)
 * @method static \Illuminate\Database\Eloquent\Builder|AuthorSaleRequest acceptedOrBelongsToAuthUser()
 * @method static \Illuminate\Database\Eloquent\Builder|AuthorSaleRequest acceptedOrBelongsToUser($user)
 * @method static \Illuminate\Database\Eloquent\Builder|AuthorSaleRequest checked()
 * @method static \Illuminate\Database\Eloquent\Builder|AuthorSaleRequest checkedAndOnCheck()
 * @method static \Illuminate\Database\Eloquent\Builder|AuthorSaleRequest checkedOrBelongsToUser($user)
 * @method static \Illuminate\Database\Eloquent\Builder|AuthorSaleRequest newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AuthorSaleRequest newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AuthorSaleRequest onCheck()
 * @method static \Illuminate\Database\Eloquent\Builder|AuthorSaleRequest onlyChecked()
 * @method static Builder|AuthorSaleRequest onlyTrashed()
 * @method static Builder|Model orderByField($column, $ids)
 * @method static Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
 * @method static \Illuminate\Database\Eloquent\Builder|AuthorSaleRequest orderStatusChangedAsc()
 * @method static \Illuminate\Database\Eloquent\Builder|AuthorSaleRequest orderStatusChangedDesc()
 * @method static \Illuminate\Database\Eloquent\Builder|AuthorSaleRequest private()
 * @method static \Illuminate\Database\Eloquent\Builder|AuthorSaleRequest query()
 * @method static \Illuminate\Database\Eloquent\Builder|AuthorSaleRequest sentOnReview()
 * @method static \Illuminate\Database\Eloquent\Builder|AuthorSaleRequest unaccepted()
 * @method static \Illuminate\Database\Eloquent\Builder|AuthorSaleRequest unchecked()
 * @method static Builder|Model void()
 * @method static \Illuminate\Database\Eloquent\Builder|AuthorSaleRequest whereAuthorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AuthorSaleRequest whereCreateUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AuthorSaleRequest whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AuthorSaleRequest whereCreator(\App\User $user)
 * @method static \Illuminate\Database\Eloquent\Builder|AuthorSaleRequest whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AuthorSaleRequest whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AuthorSaleRequest whereManagerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AuthorSaleRequest whereReviewComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AuthorSaleRequest whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AuthorSaleRequest whereStatusChangedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AuthorSaleRequest whereStatusChangedUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AuthorSaleRequest whereStatusIn($statuses)
 * @method static \Illuminate\Database\Eloquent\Builder|AuthorSaleRequest whereStatusNot($status)
 * @method static \Illuminate\Database\Eloquent\Builder|AuthorSaleRequest whereText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AuthorSaleRequest whereUpdatedAt($value)
 * @method static Builder|AuthorSaleRequest withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|AuthorSaleRequest withUnchecked()
 * @method static \Illuminate\Database\Eloquent\Builder|AuthorSaleRequest withoutCheckedScope()
 * @method static Builder|AuthorSaleRequest withoutTrashed()
 * @mixin Eloquent
 */
class AuthorSaleRequest extends Model
{
    use SoftDeletes;
    use UserCreate;
    use CheckedItems;

    public $dates = ['status_changed_at'];
    protected $fillable = [
        'text'
    ];

    static function getCachedOnModerationCount()
    {
        return Cache::tags([CacheTags::AuthorSaleRequestCount])->remember('count', 3600, function () {
            return self::sentOnReview()->count();
        });
    }

    static function flushCachedOnModerationCount()
    {
        Cache::tags([CacheTags::AuthorSaleRequestCount])->pull('count');
    }

    public function manager()
    {
        return $this->belongsTo('App\Manager', 'manager_id');
    }

    public function author()
    {
        return $this->belongsTo('App\Author');
    }
}
