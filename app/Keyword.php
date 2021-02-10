<?php

namespace App;

use App\Enums\StatusEnum;
use App\Model as Model;
use App\Scopes\CheckedScope;
use App\Traits\CheckedItems;
use App\Traits\UserCreate;
use Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;

/**
 * App\Keyword
 *
 * @property int $id
 * @property string $text
 * @property int $count
 * @property int|null $create_user_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property int|null $status
 * @property string|null $status_changed_at
 * @property int|null $status_changed_user_id
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\BookKeyword[] $book_keywords
 * @property-read \App\User|null $create_user
 * @property-read mixed $is_accepted
 * @property-read mixed $is_private
 * @property-read mixed $is_rejected
 * @property-read mixed $is_review_starts
 * @property-read mixed $is_sent_for_review
 * @property-read \App\User|null $status_changed_user
 * @method static \Illuminate\Database\Eloquent\Builder|Keyword accepted()
 * @method static \Illuminate\Database\Eloquent\Builder|Keyword acceptedAndSentForReview()
 * @method static \Illuminate\Database\Eloquent\Builder|Keyword acceptedAndSentForReviewOrBelongsToAuthUser()
 * @method static \Illuminate\Database\Eloquent\Builder|Keyword acceptedAndSentForReviewOrBelongsToUser($user)
 * @method static \Illuminate\Database\Eloquent\Builder|Keyword acceptedOrBelongsToAuthUser()
 * @method static \Illuminate\Database\Eloquent\Builder|Keyword acceptedOrBelongsToUser($user)
 * @method static \Illuminate\Database\Eloquent\Builder|Keyword any()
 * @method static \Illuminate\Database\Eloquent\Builder|Keyword checked()
 * @method static \Illuminate\Database\Eloquent\Builder|Keyword checkedAndOnCheck()
 * @method static \Illuminate\Database\Eloquent\Builder|Keyword checkedOrBelongsToUser($user)
 * @method static \Illuminate\Database\Eloquent\Builder|Keyword newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Keyword newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Keyword onCheck()
 * @method static \Illuminate\Database\Eloquent\Builder|Keyword onlyChecked()
 * @method static Builder|Keyword onlyTrashed()
 * @method static Builder|Model orderByField($column, $ids)
 * @method static Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
 * @method static \Illuminate\Database\Eloquent\Builder|Keyword orderStatusChangedAsc()
 * @method static \Illuminate\Database\Eloquent\Builder|Keyword orderStatusChangedDesc()
 * @method static \Illuminate\Database\Eloquent\Builder|Keyword private()
 * @method static \Illuminate\Database\Eloquent\Builder|Keyword query()
 * @method static \Illuminate\Database\Eloquent\Builder|Keyword search($text)
 * @method static \Illuminate\Database\Eloquent\Builder|Keyword searchFullWord($textOrArray)
 * @method static \Illuminate\Database\Eloquent\Builder|Keyword searchPartWord($textOrArray)
 * @method static \Illuminate\Database\Eloquent\Builder|Keyword sentOnReview()
 * @method static \Illuminate\Database\Eloquent\Builder|Keyword unaccepted()
 * @method static \Illuminate\Database\Eloquent\Builder|Keyword unchecked()
 * @method static Builder|Model void()
 * @method static \Illuminate\Database\Eloquent\Builder|Keyword whereCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Keyword whereCreateUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Keyword whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Keyword whereCreator(\App\User $user)
 * @method static \Illuminate\Database\Eloquent\Builder|Keyword whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Keyword whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Keyword whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Keyword whereStatusChangedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Keyword whereStatusChangedUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Keyword whereStatusIn($statuses)
 * @method static \Illuminate\Database\Eloquent\Builder|Keyword whereStatusNot($status)
 * @method static \Illuminate\Database\Eloquent\Builder|Keyword whereText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Keyword whereUpdatedAt($value)
 * @method static Builder|Keyword withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Keyword withUnchecked()
 * @method static \Illuminate\Database\Eloquent\Builder|Keyword withoutCheckedScope()
 * @method static Builder|Keyword withoutTrashed()
 * @mixin Eloquent
 */
class Keyword extends Model
{
    use SoftDeletes;
    use CheckedItems;
    use UserCreate;

    protected $attributes =
        [
            'status' => StatusEnum::Accepted
        ];

    protected $fillable = [
        'text'
    ];

    protected $visible = [
        'id',
        'text',
        'count',
        'create_user_id',
        'status',
        'status_changed_at',
        'status_changed_user_id'
    ];

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot();
    }

    public function scopeAny($query)
    {
        return $query->withoutGlobalScope(CheckedScope::class)->withTrashed();
    }

    public function scopeSearchFullWord($query, $textOrArray)
    {
        if (is_array($textOrArray)) {
            foreach ($textOrArray as $keyword) {
                $keywords[] = preg_quote(trim($keyword));
            }
            return $query->whereRaw('"text" ~* ?', ['^(' . implode('|', $keywords) . ')$']);
        } else {
            return $query->whereRaw('"text" ~* ?', ['^' . preg_quote($textOrArray) . '$']);
        }
    }

    public function scopeSearchPartWord($query, $textOrArray)
    {
        if (is_array($textOrArray)) {
            foreach ($textOrArray as $keyword) {
                $keywords[] = preg_quote(trim($keyword));
            }
            return $query->whereRaw('"text" ~* ?', ['(' . implode('|', $keywords) . ')']);
        } else {
            return $query->whereRaw('"text" ~* ?', ['' . preg_quote($textOrArray) . '']);
        }
    }

    public function scopeSearch($query, $text)
    {
        $text = trim($text);
        $text = ilikeSpecialChars($text);

        return $query->where('text', 'ilike', $text . '%');
    }

    public function setTextAttribute($value)
    {
        $value = trim($value);
        $value = replaceAsc194toAsc32($value);
        $value = preg_replace('/([[:space:]]+)/iu', ' ', $value);
        $value = preg_replace('/\.+$/iu', '', $value);
        $value = mb_ucfirst($value);

        $this->attributes['text'] = $value;
    }

    public function scopeWithUnchecked($query)
    {
        return $query->withoutGlobalScope(CheckedScope::class);
    }

    public function updateBooksCount()
    {
        $this->count = $this->book_keywords()->accepted()->count();
    }

    public function book_keywords()
    {
        return $this->hasMany('App\BookKeyword', 'keyword_id', 'id');
    }
}
