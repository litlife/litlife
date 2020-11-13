<?php

namespace App;

use App\Enums\CacheTags;
use App\Enums\StatusEnum;
use App\Model as Model;
use App\Scopes\CheckedScope;
use App\Traits\CheckedItems;
use App\Traits\UserCreate;
use Auth;
use Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

/**
 * App\BookKeyword
 *
 * @property int $id
 * @property int $book_id
 * @property int $keyword_id
 * @property int $create_user_id
 * @property int $rating
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property int|null $status
 * @property string|null $status_changed_at
 * @property int|null $status_changed_user_id
 * @property int|null $origin_book_id
 * @property-read \App\Book $book
 * @property-read \App\User $create_user
 * @property-read mixed $is_accepted
 * @property-read mixed $is_private
 * @property-read mixed $is_rejected
 * @property-read mixed $is_review_starts
 * @property-read mixed $is_sent_for_review
 * @property-read \App\Keyword|null $keyword
 * @property-read \App\User|null $status_changed_user
 * @property-read \App\BookKeywordVote|null $user_vote
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\BookKeywordVote[] $votes
 * @method static \Illuminate\Database\Eloquent\Builder|BookKeyword accepted()
 * @method static \Illuminate\Database\Eloquent\Builder|BookKeyword acceptedAndSentForReview()
 * @method static \Illuminate\Database\Eloquent\Builder|BookKeyword acceptedAndSentForReviewOrBelongsToAuthUser()
 * @method static \Illuminate\Database\Eloquent\Builder|BookKeyword acceptedAndSentForReviewOrBelongsToUser($user)
 * @method static \Illuminate\Database\Eloquent\Builder|BookKeyword acceptedOrBelongsToAuthUser()
 * @method static \Illuminate\Database\Eloquent\Builder|BookKeyword acceptedOrBelongsToUser($user)
 * @method static \Illuminate\Database\Eloquent\Builder|BookKeyword checked()
 * @method static \Illuminate\Database\Eloquent\Builder|BookKeyword checkedAndOnCheck()
 * @method static \Illuminate\Database\Eloquent\Builder|BookKeyword checkedOrBelongsToUser($user)
 * @method static \Illuminate\Database\Eloquent\Builder|BookKeyword joinKeywords()
 * @method static \Illuminate\Database\Eloquent\Builder|BookKeyword newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BookKeyword newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BookKeyword onCheck()
 * @method static \Illuminate\Database\Eloquent\Builder|BookKeyword onlyChecked()
 * @method static Builder|BookKeyword onlyTrashed()
 * @method static Builder|Model orderByField($column, $ids)
 * @method static Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
 * @method static \Illuminate\Database\Eloquent\Builder|BookKeyword orderStatusChangedAsc()
 * @method static \Illuminate\Database\Eloquent\Builder|BookKeyword orderStatusChangedDesc()
 * @method static \Illuminate\Database\Eloquent\Builder|BookKeyword private()
 * @method static \Illuminate\Database\Eloquent\Builder|BookKeyword query()
 * @method static \Illuminate\Database\Eloquent\Builder|BookKeyword search($text)
 * @method static \Illuminate\Database\Eloquent\Builder|BookKeyword searchFullWord($textOrArray)
 * @method static \Illuminate\Database\Eloquent\Builder|BookKeyword searchPartWord($textOrArray)
 * @method static \Illuminate\Database\Eloquent\Builder|BookKeyword sentOnReview()
 * @method static \Illuminate\Database\Eloquent\Builder|BookKeyword unaccepted()
 * @method static \Illuminate\Database\Eloquent\Builder|BookKeyword unchecked()
 * @method static Builder|Model void()
 * @method static \Illuminate\Database\Eloquent\Builder|BookKeyword whereBookId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BookKeyword whereCreateUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BookKeyword whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BookKeyword whereCreator(\App\User $user)
 * @method static \Illuminate\Database\Eloquent\Builder|BookKeyword whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BookKeyword whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BookKeyword whereKeywordId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BookKeyword whereOriginBookId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BookKeyword whereRating($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BookKeyword whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BookKeyword whereStatusChangedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BookKeyword whereStatusChangedUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BookKeyword whereStatusIn($statuses)
 * @method static \Illuminate\Database\Eloquent\Builder|BookKeyword whereStatusNot($status)
 * @method static \Illuminate\Database\Eloquent\Builder|BookKeyword whereUpdatedAt($value)
 * @method static Builder|BookKeyword withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|BookKeyword withUnchecked()
 * @method static \Illuminate\Database\Eloquent\Builder|BookKeyword withoutCheckedScope()
 * @method static Builder|BookKeyword withoutTrashed()
 * @mixin Eloquent
 */
class BookKeyword extends Model
{
    use SoftDeletes;
    use CheckedItems;
    use UserCreate;

    protected $fillable = [
        'keyword_id',
        'book_id'
    ];

    protected $attributes =
        [
            'status' => StatusEnum::Private
        ];

    public static function boot()
    {
        parent::boot();

        static::addGlobalScope(new CheckedScope);
    }

    static function getCachedOnModerationCount()
    {
        return Cache::tags([CacheTags::BookKeywordsOnModerationCount])->remember('count', 3600, function () {
            return self::sentOnReview()->count();
        });
    }

    static function flushCachedOnModerationCount()
    {
        Cache::tags([CacheTags::BookKeywordsOnModerationCount])->pull('count');
    }

    public function keyword()
    {
        return $this->hasOne('App\Keyword', 'id', 'keyword_id');
    }

    public function book()
    {
        return $this->belongsTo('App\Book', 'book_id', 'id');
    }

    public function votes()
    {
        return $this->hasMany('App\BookKeywordVote', 'book_keyword_id', 'id');
    }

    public function user_vote()
    {
        return $this->hasOne('App\BookKeywordVote')
            ->where("create_user_id", Auth::id());
    }

    public function scopeSearchFullWord($query, $textOrArray)
    {
        if (is_array($textOrArray)) {
            foreach ($textOrArray as $keyword) {
                $keywords[] = preg_quote(trim($keyword));
            }
            return $query->whereRaw('"text" ~* \'^(' . implode('|', $keywords) . ')$\'');
        } else {
            return $query->whereRaw('"text" ~* \'^' . preg_quote($textOrArray) . '$\'');
        }
    }

    public function scopeSearchPartWord($query, $textOrArray)
    {
        if (is_array($textOrArray)) {
            foreach ($textOrArray as $keyword) {
                $keywords[] = preg_quote(trim($keyword));
            }
            return $query->whereRaw('"text" ~* \'(' . implode('|', $keywords) . ')\'');
        } else {
            return $query->whereRaw('"text" ~* \'' . preg_quote($textOrArray) . '\'');
        }
    }

    public function scopeSearch($query, $text)
    {
        $text = trim($text);

        return $query->where('text', 'ilike', $text . '%');
    }

    public function scopeJoinKeywords($query)
    {
        return $query->join('keywords', 'book_keywords.keyword_id', '=', 'keywords.id');
    }
}
