<?php

namespace App;

use App\Model as Model;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;


/**
 * App\BookStatus
 *
 * @property int $book_id
 * @property int $user_id
 * @property Carbon|null $user_updated_at
 * @property int $id
 * @property int $status
 * @property int|null $origin_book_id
 * @property-read \App\Book $book
 * @property mixed $code
 * @property-read \App\Book|null $originBook
 * @property-read \App\User $user
 * @method static Builder|BookStatus newModelQuery()
 * @method static Builder|BookStatus newQuery()
 * @method static Builder|BookStatus onlyTrashed()
 * @method static Builder|Model orderByField($column, $ids)
 * @method static Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
 * @method static Builder|BookStatus query()
 * @method static Builder|Model void()
 * @method static Builder|BookStatus whereBookId($value)
 * @method static Builder|BookStatus whereCode($value)
 * @method static Builder|BookStatus whereId($value)
 * @method static Builder|BookStatus whereOriginBookId($value)
 * @method static Builder|BookStatus whereStatus($value)
 * @method static Builder|BookStatus whereUserId($value)
 * @method static Builder|BookStatus whereUserUpdatedAt($value)
 * @mixin Eloquent
 */
class BookStatus extends Model
{
    //protected $table = 'book_statuses';

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'book_id',
        'status',
        'user_updated_at',
        'origin_book_id'
    ];

    protected $dates = [
        'user_updated_at'
    ];

    public function scopeOnlyTrashed($query)
    {
        return $query->where('status', 'null')->orWhereNull('status');
    }

    public function book()
    {
        return $this->belongsTo('App\Book', 'book_id', 'id');
    }

    public function originBook()
    {
        return $this->belongsTo('App\Book', 'origin_book_id', 'id')->any();
    }

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id', 'id');
    }

    public function scopeWhereCode($query, $value)
    {
        return $query->where('code', $value);
    }

    public function getCodeAttribute($value)
    {
        return $value;
    }

    public function setCodeAttribute($value)
    {
        $this->attributes['code'] = $value;
    }

    public function getStatusAttribute($value)
    {
        if ($value == 'null') {
            return null;
        }

        return $value;
    }

    public function setStatusAttribute($value)
    {
        $this->attributes['status'] = $value;
    }
}
