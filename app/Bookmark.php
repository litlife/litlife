<?php

namespace App;

use App\Model as Model;
use App\Traits\UserCreate;
use Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;
use Litlife\Url\Url;

/**
 * App\Bookmark
 *
 * @property int $id
 * @property int $create_user_id
 * @property string|null $old_url_old
 * @property string $title
 * @property int $old_time
 * @property int|null $folder_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property string $url
 * @property bool $new
 * @property-read \App\BookmarkFolder $bookmark_folder
 * @property-read \App\User $create_user
 * @property-read \App\BookmarkFolder|null $folder
 * @method static \Illuminate\Database\Eloquent\Builder|Bookmark newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Bookmark newQuery()
 * @method static Builder|Bookmark onlyTrashed()
 * @method static Builder|Model orderByField($column, $ids)
 * @method static Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
 * @method static \Illuminate\Database\Eloquent\Builder|Bookmark query()
 * @method static Builder|Model void()
 * @method static \Illuminate\Database\Eloquent\Builder|Bookmark whereCreateUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bookmark whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bookmark whereCreator(\App\User $user)
 * @method static \Illuminate\Database\Eloquent\Builder|Bookmark whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bookmark whereFolderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bookmark whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bookmark whereNew($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bookmark whereOldTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bookmark whereOldUrlOld($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bookmark whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bookmark whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Bookmark whereUrl($value)
 * @method static Builder|Bookmark withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Bookmark withoutFolder()
 * @method static Builder|Bookmark withoutTrashed()
 * @mixin Eloquent
 */
class Bookmark extends Model
{
	use UserCreate;
	use SoftDeletes;

	protected $fillable = [
		'url',
		'title',
		'folder_id'
	];

	protected $visible = [
		'id',
		'url',
		'title',
		'folder_id',
		'created_at',
		'updated_at',
		'deleted_at'
	];

	public function bookmark_folder()
	{
		return $this->belongsTo('App\BookmarkFolder');
	}

	public function folder()
	{
		return $this->belongsTo('App\BookmarkFolder', 'folder_id', 'id');
	}

	public function scopeWithoutFolder($query)
	{
		return $query->whereNull('folder_id');
	}

	public function setUrlAttribute($value)
	{
		$value = (string)Url::fromString($value)
			->getPathQueryFragment()
			->withoutFragment();

		if (mb_substr($value, 0, 1) != '/')
			$value = '/' . $value;

		$this->attributes['url'] = $value;
	}

	public function getUrlAttribute($value)
	{
		$value = (string)Url::fromString($value)
			->getPathQueryFragment()
			->withoutFragment();

		if (mb_substr($value, 0, 1) != '/')
			$value = '/' . $value;

		return $value;
	}
}
