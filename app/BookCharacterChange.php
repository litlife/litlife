<?php

namespace App;

use App\Model as Model;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

/**
 * App\BookCharacterChange
 *
 * @property int $id
 * @property int $sum Количество символов, которое прибавилось или убавилось. Может быть положительным или отрицательным
 * @property int $book_id ID книги
 * @property int $section_id ID главы
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read \App\Book $book
 * @property-read \App\Section $section
 * @method static Builder|BookCharacterChange newModelQuery()
 * @method static Builder|BookCharacterChange newQuery()
 * @method static Builder|Model orderByField($column, $ids)
 * @method static Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
 * @method static Builder|BookCharacterChange query()
 * @method static Builder|Model void()
 * @method static Builder|BookCharacterChange whereBookId($value)
 * @method static Builder|BookCharacterChange whereCreatedAt($value)
 * @method static Builder|BookCharacterChange whereId($value)
 * @method static Builder|BookCharacterChange whereSectionId($value)
 * @method static Builder|BookCharacterChange whereSum($value)
 * @method static Builder|BookCharacterChange whereUpdatedAt($value)
 * @mixin Eloquent
 */
class BookCharacterChange extends Model
{
	function book()
	{
		return $this->belongsTo('App\Book')->any();
	}

	function section()
	{
		return $this->belongsTo('App\Section')->any();
	}
}
