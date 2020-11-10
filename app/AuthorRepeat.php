<?php

namespace App;

use App\Model as Model;
use App\Traits\UserCreate;
use Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

/**
 * App\AuthorRepeat
 *
 * @property int $id
 * @property int $create_user_id
 * @property int $time
 * @property string|null $comment
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|Author[] $authors
 * @property-read User $create_user
 * @method static \Illuminate\Database\Eloquent\Builder|AuthorRepeat newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AuthorRepeat newQuery()
 * @method static Builder|AuthorRepeat onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByField($column, $ids)
 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
 * @method static \Illuminate\Database\Eloquent\Builder|AuthorRepeat query()
 * @method static \Illuminate\Database\Eloquent\Builder|Model void()
 * @method static \Illuminate\Database\Eloquent\Builder|AuthorRepeat whereComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AuthorRepeat whereCreateUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AuthorRepeat whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AuthorRepeat whereCreator(User $user)
 * @method static \Illuminate\Database\Eloquent\Builder|AuthorRepeat whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AuthorRepeat whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AuthorRepeat whereTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AuthorRepeat whereUpdatedAt($value)
 * @method static Builder|AuthorRepeat withTrashed()
 * @method static Builder|AuthorRepeat withoutTrashed()
 * @mixin Eloquent
 */
class AuthorRepeat extends Model
{
    use SoftDeletes;
    use UserCreate;

    protected $fillable = [
        'comment'
    ];

    static function getCachedOnModerationCount()
    {
        return Cache::tags(['author_repeat_on_moderation_count'])->remember('count', 3600, function () {
            return self::count();
        });
    }

    static function flushCachedOnModerationCount()
    {
        Cache::tags(['author_repeat_on_moderation_count'])->pull('count');
    }

    public function authors()
    {
        return $this->belongsToMany('App\Author', 'author_repeat_pivots')
            ->notMerged();
    }
}
