<?php

namespace App;

use App\Model as Model;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;

/**
 * App\BookReadRememberPage
 *
 * @property int $book_id
 * @property int $user_id
 * @property int $old_time
 * @property int $page
 * @property string $updated_at
 * @property int|null $inner_section_id
 * @property int|null $characters_count Количество символов в тексте книги на момент последнего прочтения
 * @property-read \App\Book $book
 * @property-read \App\User $user
 * @method static Builder|BookReadRememberPage newModelQuery()
 * @method static Builder|BookReadRememberPage newQuery()
 * @method static Builder|Model orderByField($column, $ids)
 * @method static Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
 * @method static Builder|BookReadRememberPage query()
 * @method static Builder|Model void()
 * @method static Builder|BookReadRememberPage whereBookId($value)
 * @method static Builder|BookReadRememberPage whereCharactersCount($value)
 * @method static Builder|BookReadRememberPage whereInnerSectionId($value)
 * @method static Builder|BookReadRememberPage whereOldTime($value)
 * @method static Builder|BookReadRememberPage wherePage($value)
 * @method static Builder|BookReadRememberPage whereUpdatedAt($value)
 * @method static Builder|BookReadRememberPage whereUserId($value)
 * @mixin Eloquent
 */
class BookReadRememberPage extends Model
{
	public $incrementing = false;
	public $timestamps = false;
	protected $primaryKey = ['book_id', 'user_id'];
	protected $fillable = [
		'book_id',
		'user_id',
		'page',
		'inner_section_id',
		'characters_count'
	];

	public static function boot()
	{
		parent::boot();

		static::creating(function ($model) {
			$model->updated_at = $model->freshTimestamp();
		});
	}

	public function book()
	{
		return $this->belongsTo('App\Book');
	}

	public function user()
	{
		return $this->belongsTo('App\User');
	}
}
