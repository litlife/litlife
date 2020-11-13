<?php

namespace App;

use App\Model as Model;
use App\Traits\ImageResizable;
use App\Traits\UserCreate;
use Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;
use Litlife\IdDirname\IdDirname;
use Litlife\Url\Url;

// use IgnorableObservers\IgnorableObservers;

/**
 * App\Image
 *
 * @property int $id
 * @property string $type
 * @property int $create_user_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property string $name
 * @property int $size
 * @property string|null $md5
 * @property string $storage
 * @property string|null $dirname
 * @property string|null $sha256_hash
 * @property string|null $phash
 * @property-read \App\User $create_user
 * @property-read mixed $full_url200x200
 * @property-read mixed $full_url50x50
 * @property-read mixed $full_url90x90
 * @property-read mixed $full_url
 * @property-read mixed $url
 * @property-read mixed $full_url_sized
 * @property-write mixed $max_height
 * @property-write mixed $max_width
 * @property-write mixed $quality
 * @method static \Illuminate\Database\Eloquent\Builder|Image any()
 * @method static \Illuminate\Database\Eloquent\Builder|Image md5Hash($hash)
 * @method static \Illuminate\Database\Eloquent\Builder|Image newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Image newQuery()
 * @method static Builder|Image onlyTrashed()
 * @method static Builder|Model orderByField($column, $ids)
 * @method static Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
 * @method static \Illuminate\Database\Eloquent\Builder|Image pHash($hash)
 * @method static \Illuminate\Database\Eloquent\Builder|Image query()
 * @method static \Illuminate\Database\Eloquent\Builder|Image setSize($height)
 * @method static \Illuminate\Database\Eloquent\Builder|Image sha256Hash($hash)
 * @method static Builder|Model void()
 * @method static \Illuminate\Database\Eloquent\Builder|Image whereCreateUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Image whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Image whereCreator(\App\User $user)
 * @method static \Illuminate\Database\Eloquent\Builder|Image whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Image whereDirname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Image whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Image whereMd5($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Image whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Image wherePhash($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Image whereSha256Hash($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Image whereSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Image whereStorage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Image whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Image whereUpdatedAt($value)
 * @method static Builder|Image withTrashed()
 * @method static Builder|Image withoutTrashed()
 * @mixin Eloquent
 */
class Image extends Model
{
    use UserCreate;
    use SoftDeletes;
    use ImageResizable;

    public $folder = '_i';
    public $img;
    public $source;

    protected $appends = ['fullUrlSized', 'url'];

    public function scopeAny($query)
    {
        return $query->withTrashed();
    }

    public function scopeSetSize($width, $height)
    {
        $this->maxWidth = $width;
        $this->maxHeight = $height;
    }

    public function scopeSha256Hash($query, $hash)
    {
        return $query->where('sha256_hash', $hash);
    }

    public function scopeMd5Hash($query, $hash)
    {
        return $query->where('md5', $hash);
    }

    public function scopePHash($query, $hash)
    {
        return $query->where('phash', $hash);
    }

    /*
    public function getWidthAttribute($query)
    {

    }

    public function getHeightAttribute($query)
    {

    }
    */

    public function getDirname()
    {
        $idDirname = new IdDirname($this->id);

        $url = (new Url)->withDirname('images/' . implode('/', $idDirname->getDirnameArrayEncoded()));

        return $url->getPath();
    }
}
