<?php

namespace App;

use App\Model as Model;
use Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;
use Stevebauman\Purify\Facades\Purify;

/**
 * App\AuthorBiography
 *
 * @property int|null $author_id
 * @property string $text
 * @property int $edit_user_id
 * @property int $edit_time
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property Carbon|null $user_edited_at Время когда пользователь отредактировал
 * @property int $id
 * @property bool $external_images_downloaded Скачаны ли внешние изображения
 * @property-read \App\Author|null $author
 * @property-read \App\User|null $edit_user
 * @method static \Illuminate\Database\Eloquent\Builder|AuthorBiography newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AuthorBiography newQuery()
 * @method static Builder|AuthorBiography onlyTrashed()
 * @method static Builder|Model orderByField($column, $ids)
 * @method static Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
 * @method static \Illuminate\Database\Eloquent\Builder|AuthorBiography query()
 * @method static Builder|Model void()
 * @method static \Illuminate\Database\Eloquent\Builder|AuthorBiography whereAuthorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AuthorBiography whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AuthorBiography whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AuthorBiography whereEditTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AuthorBiography whereEditUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AuthorBiography whereExternalImagesDownloaded($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AuthorBiography whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AuthorBiography whereText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AuthorBiography whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AuthorBiography whereUserEditedAt($value)
 * @method static Builder|AuthorBiography withTrashed()
 * @method static Builder|AuthorBiography withoutTrashed()
 * @mixin Eloquent
 */
class AuthorBiography extends Model
{
	use SoftDeletes;

	protected $fillable = [
		'text',
		'author_id'
	];

	protected $dates = [
		'user_edited_at'
	];

	public function author()
	{
		return $this->belongsTo('App\Author', 'author_id', 'id');
	}

	public function edit_user()
	{
		return $this->hasOne('App\User', 'id', 'edit_user_id');
	}

	public function getTextAttribute($value)
	{
		/*
		$value = preg_replace_callback('/\<img(.*)\>/iuU', function ($m) {
			$s = $m[1];
			if (preg_match('/(.*)class(?:[[:space:]]*)=(?:[[:space:]]*)"([^\"]*)"(.*)/iu', $s, $m)) {
				return '<img '.$m[1].' class="img-responsive '.$m[2].'" ' . $m[3] . '>';
			}
			else {
				return '<img class="img-responsive" ' . $s . '>';
			}
		}, $value);
  */
		return $value;
	}

	public function setTextAttribute($value)
	{
		$value = trim($value);

		$configuration = [
			'Attr.EnableID' => true,
			'HTML.Allowed' =>
				'div,p,span,h1,h2,h3,h4,h5,h6,a[href|name],img[width|height|src|alt],blockquote,strong,em,sub,sup,*[class|style|id],dl,dt,dd,' .
				'table[summary],caption,col,colgroup,tbody,td[abbr],tfoot,th[abbr],thead,tr,hr'
		];

		$this->attributes['text'] = Purify::clean($value, array_merge(config('purify.settings'), $configuration));
	}

}
