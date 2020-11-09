<?php

namespace App;

use App\Traits\UserCreate;

/**
 * App\CollectedBook
 *
 * @property int $id
 * @property int $collection_id ID подборки
 * @property int $book_id Книга
 * @property int $create_user_id collected_books.create_user_id
 * @property int|null $number Номер
 * @property string|null $comment Комментарий
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Book $book
 * @property-read \App\Collection $collection
 * @property-read \App\User $create_user
 * @method static \Illuminate\Database\Eloquent\Builder|CollectedBook newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CollectedBook newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByField($column, $ids)
 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
 * @method static \Illuminate\Database\Eloquent\Builder|CollectedBook query()
 * @method static \Illuminate\Database\Eloquent\Builder|Model void()
 * @method static \Illuminate\Database\Eloquent\Builder|CollectedBook whereBookId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CollectedBook whereCollectionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CollectedBook whereComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CollectedBook whereCreateUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CollectedBook whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CollectedBook whereCreator(\App\User $user)
 * @method static \Illuminate\Database\Eloquent\Builder|CollectedBook whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CollectedBook whereNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CollectedBook whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class CollectedBook extends Model
{
	use UserCreate;

	protected $table = 'collected_books';

	protected $fillable = [
		'collection_id',
		'book_id',
		'number',
		'comment'
	];

	public function book()
	{
		return $this->belongsTo('App\Book');
	}

	public function collection()
	{
		return $this->belongsTo('App\Collection');
	}
}
