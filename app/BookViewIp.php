<?php

namespace App;

use App\Model as Model;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;

/**
 * App\BookViewIp
 *
 * @property string $ip
 * @property int $book_id
 * @property int $count
 * @property-read Book $book
 * @method static Builder|BookViewIp newModelQuery()
 * @method static Builder|BookViewIp newQuery()
 * @method static Builder|Model orderByField($column, $ids)
 * @method static Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
 * @method static Builder|BookViewIp query()
 * @method static Builder|Model void()
 * @method static Builder|BookViewIp whereBookId($value)
 * @method static Builder|BookViewIp whereCount($value)
 * @method static Builder|BookViewIp whereIp($value)
 * @mixin Eloquent
 */
class BookViewIp extends Model
{
    public $timestamps = false;
    public $incrementing = false;
    protected $fillable = [
        'ip',
        'book_id'
    ];
    protected $primaryKey = null;

    public function book()
    {
        return $this->belongsTo('App\Book', 'book_id', 'id');
    }
}
