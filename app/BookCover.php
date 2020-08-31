<?php

namespace App;

use App\Model as Model;
use App\Traits\UserCreate;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;

/**
 * App\BookCover
 *
 * @property int $book_id
 * @property string $name
 * @property int $size
 * @property int $time
 * @property int $width
 * @property int $height
 * @property int $type
 * @property string $storage
 * @property string|null $dirname
 * @property int|null $create_user_id
 * @property-read \App\User|null $create_user
 * @method static Builder|BookCover newModelQuery()
 * @method static Builder|BookCover newQuery()
 * @method static Builder|Model orderByField($column, $ids)
 * @method static Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
 * @method static Builder|BookCover query()
 * @method static Builder|BookCover void()
 * @method static Builder|BookCover whereBookId($value)
 * @method static Builder|BookCover whereCreateUserId($value)
 * @method static Builder|BookCover whereCreator(\App\User $user)
 * @method static Builder|BookCover whereDirname($value)
 * @method static Builder|BookCover whereHeight($value)
 * @method static Builder|BookCover whereName($value)
 * @method static Builder|BookCover whereSize($value)
 * @method static Builder|BookCover whereStorage($value)
 * @method static Builder|BookCover whereTime($value)
 * @method static Builder|BookCover whereType($value)
 * @method static Builder|BookCover whereWidth($value)
 * @mixin Eloquent
 */
class BookCover extends Model
{
	use UserCreate;

	public function scopeVoid($query)
	{
		return $query;
	}
}
