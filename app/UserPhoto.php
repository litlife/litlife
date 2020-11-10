<?php

namespace App;

use App\Traits\ImageResizable;
use App\Traits\UserCreate;
use Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;

/**
 * App\UserPhoto
 *
 * @property int $id
 * @property int $user_id
 * @property string $name
 * @property int $size
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property object|null $parameters
 * @property string $storage
 * @property string|null $dirname
 * @property string|null $md5
 * @property-read User $create_user
 * @property-read mixed $full_url200x200
 * @property-read mixed $full_url50x50
 * @property-read mixed $full_url90x90
 * @property-read mixed $full_url
 * @property-read mixed $url
 * @property-read mixed $full_url_sized
 * @property-write mixed $max_height
 * @property-write mixed $max_width
 * @property-write mixed $path_to_file
 * @property-write mixed $quality
 * @property-read User $user
 * @method static \Illuminate\Database\Eloquent\Builder|UserPhoto newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserPhoto newQuery()
 * @method static Builder|UserPhoto onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByField($column, $ids)
 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
 * @method static \Illuminate\Database\Eloquent\Builder|UserPhoto query()
 * @method static \Illuminate\Database\Eloquent\Builder|Model void()
 * @method static \Illuminate\Database\Eloquent\Builder|UserPhoto whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserPhoto whereCreator(User $user)
 * @method static \Illuminate\Database\Eloquent\Builder|UserPhoto whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserPhoto whereDirname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserPhoto whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserPhoto whereMd5($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserPhoto whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserPhoto whereParameters($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserPhoto whereSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserPhoto whereStorage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserPhoto whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserPhoto whereUserId($value)
 * @method static Builder|UserPhoto withTrashed()
 * @method static Builder|UserPhoto withoutTrashed()
 * @mixin Eloquent
 */
class UserPhoto extends Model
{
    use SoftDeletes;
    use ImageResizable;
    use UserCreate;


    const CREATE_USER_ID = 'user_id';
    public $folder = '_user';
    public $source;
    protected $casts = [
        'parameters' => 'object'
    ];

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id', 'id');
    }

    public function setPathToFileAttribute($path_to_file)
    {
        $this->path_to_file = $path_to_file;
    }

    public function getWidth()
    {
        return empty($this->parameters->w) ? null : $this->parameters->w;
    }

    public function getHeight()
    {
        return empty($this->parameters->h) ? null : $this->parameters->h;
    }
}
