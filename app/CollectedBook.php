<?php

namespace App;

use App\Traits\UserCreate;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

/**
 * App\CollectedBook
 *
 * @property int $id
 * @property int $collection_id ID подборки
 * @property int $book_id Книга
 * @property int $create_user_id collected_books.create_user_id
 * @property int|null $number Номер
 * @property string|null $comment Комментарий
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Book $book
 * @property-read Collection $collection
 * @property-read User $create_user
 * @method static Builder|CollectedBook newModelQuery()
 * @method static Builder|CollectedBook newQuery()
 * @method static Builder|Model orderByField($column, $ids)
 * @method static Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
 * @method static Builder|CollectedBook query()
 * @method static Builder|Model void()
 * @method static Builder|CollectedBook whereBookId($value)
 * @method static Builder|CollectedBook whereCollectionId($value)
 * @method static Builder|CollectedBook whereComment($value)
 * @method static Builder|CollectedBook whereCreateUserId($value)
 * @method static Builder|CollectedBook whereCreatedAt($value)
 * @method static Builder|CollectedBook whereCreator(User $user)
 * @method static Builder|CollectedBook whereId($value)
 * @method static Builder|CollectedBook whereNumber($value)
 * @method static Builder|CollectedBook whereUpdatedAt($value)
 * @mixin Eloquent
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
