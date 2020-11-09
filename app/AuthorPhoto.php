<?php

namespace App;

use App\Model as Model;
use App\Traits\FileSizes;
use App\Traits\ImageResizable;
use App\Traits\UserCreate;
use Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;

/**
 * App\AuthorPhoto
 *
 * @property int $author_id
 * @property int $type
 * @property string $name
 * @property int $size
 * @property int $time
 * @property int $width
 * @property int $height
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property int $id
 * @property string $storage
 * @property string|null $dirname
 * @property int|null $create_user_id
 * @property-read \App\Author $author
 * @property-read \App\User|null $create_user
 * @property-read mixed $full_url200x200
 * @property-read mixed $full_url50x50
 * @property-read mixed $full_url90x90
 * @property-read mixed $full_url
 * @property-read mixed $url
 * @property-read mixed $full_url_sized
 * @property-write mixed $max_height
 * @property-write mixed $max_width
 * @property-write mixed $quality
 * @method static \Illuminate\Database\Eloquent\Builder|AuthorPhoto newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AuthorPhoto newQuery()
 * @method static Builder|AuthorPhoto onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByField($column, $ids)
 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
 * @method static \Illuminate\Database\Eloquent\Builder|AuthorPhoto query()
 * @method static \Illuminate\Database\Eloquent\Builder|Model void()
 * @method static \Illuminate\Database\Eloquent\Builder|AuthorPhoto whereAuthorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AuthorPhoto whereCreateUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AuthorPhoto whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AuthorPhoto whereCreator(\App\User $user)
 * @method static \Illuminate\Database\Eloquent\Builder|AuthorPhoto whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AuthorPhoto whereDirname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AuthorPhoto whereHeight($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AuthorPhoto whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AuthorPhoto whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AuthorPhoto whereSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AuthorPhoto whereStorage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AuthorPhoto whereTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AuthorPhoto whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AuthorPhoto whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AuthorPhoto whereWidth($value)
 * @method static Builder|AuthorPhoto withTrashed()
 * @method static Builder|AuthorPhoto withoutTrashed()
 * @mixin Eloquent
 */
class AuthorPhoto extends Model
{
	use SoftDeletes;
	use ImageResizable;
	use UserCreate;

	public $folder = '_author';
	public $source;

	public function author()
	{
		return $this->belongsTo('App\Author');
	}

	public function getWidth()
	{
		return $this->width;
	}

	public function getHeight()
	{
		return $this->height;
	}
}
