<?php

namespace App;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;

use Illuminate\Support\Carbon;

/**
 * App\PriceChangeLog
 *
 * @property int $id
 * @property int $book_id ID книги
 * @property float|null $price Цена
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|PriceChangeLog newModelQuery()
 * @method static Builder|PriceChangeLog newQuery()
 * @method static Builder|Model orderByField($column, $ids)
 * @method static Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
 * @method static Builder|PriceChangeLog query()
 * @method static Builder|Model void()
 * @method static Builder|PriceChangeLog whereBookId($value)
 * @method static Builder|PriceChangeLog whereCreatedAt($value)
 * @method static Builder|PriceChangeLog whereId($value)
 * @method static Builder|PriceChangeLog wherePrice($value)
 * @method static Builder|PriceChangeLog whereUpdatedAt($value)
 * @mixin Eloquent
 */
class PriceChangeLog extends Model
{
    protected $fillable = [
        'price'
    ];

    public function setPriceAttribute($value)
    {
        if (empty($value)) {
            $this->attributes['price'] = null;
        } else {
            $this->attributes['price'] = number_format($value, 2, '.', '');
        }
    }

    public function getPriceAttribute($value)
    {
        $integer_value = (integer)$value;

        if ($value == $integer_value) {
            return $integer_value;
        } else {
            return (float)$value;
        }
    }
}
