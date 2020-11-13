<?php

namespace App;

use App\Model as Model;
use App\Traits\UserCreate;
use Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;

/**
 * App\BookmarkFolder
 *
 * @property int $id
 * @property int $create_user_id
 * @property string $title
 * @property int $bookmark_count
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Bookmark[] $bookmarks
 * @property-read \App\User $create_user
 * @method static \Illuminate\Database\Eloquent\Builder|BookmarkFolder newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BookmarkFolder newQuery()
 * @method static Builder|BookmarkFolder onlyTrashed()
 * @method static Builder|Model orderByField($column, $ids)
 * @method static Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
 * @method static \Illuminate\Database\Eloquent\Builder|BookmarkFolder query()
 * @method static Builder|Model void()
 * @method static \Illuminate\Database\Eloquent\Builder|BookmarkFolder whereBookmarkCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BookmarkFolder whereCreateUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BookmarkFolder whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BookmarkFolder whereCreator(\App\User $user)
 * @method static \Illuminate\Database\Eloquent\Builder|BookmarkFolder whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BookmarkFolder whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BookmarkFolder whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BookmarkFolder whereUpdatedAt($value)
 * @method static Builder|BookmarkFolder withTrashed()
 * @method static Builder|BookmarkFolder withoutTrashed()
 * @mixin Eloquent
 */
class BookmarkFolder extends Model
{
    use UserCreate;
    use SoftDeletes;

    protected $fillable = [
        'title',
        'folder_id'
    ];

    protected $visible = [
        'id',
        'title',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public static function boot()
    {
        static::Creating(function ($bookmarkFolder) {

            $bookmarkFolder->autoAssociateAuthUser();

        });

        parent::boot();
    }

    public function bookmarks()
    {
        return $this->hasMany('App\Bookmark', 'folder_id', 'id');
    }
}
