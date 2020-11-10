<?php

namespace App;

use App\Model as Model;
use App\Traits\ImageResizable;
use App\Traits\UserCreate;
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;

/**
 * App\Attachment
 *
 * @property int $id
 * @property int $book_id
 * @property string $name
 * @property string $content_type
 * @property int $size
 * @property string $type
 * @property array|null $parameters
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property string $storage
 * @property string|null $dirname
 * @property int|null $create_user_id
 * @property string|null $sha256_hash
 * @property-read Book $book
 * @property-read User|null $create_user
 * @property-read mixed $full_url200x200
 * @property-read mixed $full_url50x50
 * @property-read mixed $full_url90x90
 * @property-read mixed $full_url
 * @property-read mixed $path_to_file
 * @property-read mixed $url
 * @property-read mixed $full_url_sized
 * @property-write mixed $max_height
 * @property-write mixed $max_width
 * @property-write mixed $quality
 * @method static \Illuminate\Database\Eloquent\Builder|Attachment inBook($bookId)
 * @method static \Illuminate\Database\Eloquent\Builder|Attachment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Attachment newQuery()
 * @method static Builder|Attachment onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByField($column, $ids)
 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
 * @method static \Illuminate\Database\Eloquent\Builder|Attachment parametersIn($var, $array)
 * @method static \Illuminate\Database\Eloquent\Builder|Attachment query()
 * @method static \Illuminate\Database\Eloquent\Builder|Model void()
 * @method static \Illuminate\Database\Eloquent\Builder|Attachment whereBookId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attachment whereContentType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attachment whereCreateUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attachment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attachment whereCreator(User $user)
 * @method static \Illuminate\Database\Eloquent\Builder|Attachment whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attachment whereDirname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attachment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attachment whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attachment whereParameters($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attachment whereSha256Hash($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attachment whereSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attachment whereStorage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attachment whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Attachment whereUpdatedAt($value)
 * @method static Builder|Attachment withTrashed()
 * @method static Builder|Attachment withoutTrashed()
 * @mixin Eloquent
 */
class Attachment extends Model
{
    use SoftDeletes;
    use ImageResizable;
    use UserCreate;

    public $source;
    public $visible = [
        'id',
        'book_id',
        'name',
        'content_type',
        'size',
        'type',
        'parameters',
        'created_at',
        'updated_at',
        'deleted_at',
        'create_user_id',
        'sha256_hash'
    ];
    public $content;
    public $filePath;
    public $folder = '_ba';
    protected $casts = [
        'parameters' => 'array'
    ];

    public function book()
    {
        return $this->belongsTo('App\Book')->any();
    }

    public function scopeInBook($query, $bookId)
    {
        return $query->where('book_id', '=', $bookId);
    }

    public function setContentTypeAttribute($value)
    {
        $value = trim($value);

        if ($value == '') {
            throw new Exception('content-type вложения не должен быть пустым');
        }

        $this->attributes['content_type'] = $value;
    }

    public function getPathToFileAttribute()
    {
        $model = &$this;

        return getPath($model->book_id) . '/' . $model->folder . '/' . $model->name;
    }

    public function scopeParametersIn($query, $var, $array)
    {
        $array = (array)$array;

        return $query->where(function ($query) use ($var, $array) {
            foreach ($array as $value) {
                $query->orWhereRaw('"parameters"::jsonb @> ?', [json_encode([$var => $value])]);
            }
        });
    }

    public function getWidth()
    {
        return empty($this->parameters['w']) ? null : $this->parameters['w'];
    }

    public function getHeight()
    {
        return empty($this->parameters['h']) ? null : $this->parameters['h'];
    }

    public function addParameter($key, $value)
    {
        if (!empty($key)) {
            $arr = $this->parameters ?? [];
            $arr[$key] = $value;
            $this->parameters = $arr;
        }
    }

    public function getParameter($key)
    {
        if (isset($this->parameters[$key])) {
            return $this->parameters[$key];
        } else {
            return null;
        }
    }

    public function isExists()
    {
        return $this->exists();
    }

    public function isCover()
    {
        return (boolean)($this->book->cover_id == $this->id);
    }

    public function scopeWhereSha256Hash($query, $hash)
    {
        if (is_array($hash)) {
            return $query->whereIn('sha256_hash', $hash);
        } else {
            return $query->where('sha256_hash', $hash);
        }
    }
}
