<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\PriceChangeLog
 *
 * @property int $id
 * @property int $book_id ID книги
 * @property float|null $price Цена
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|PriceChangeLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PriceChangeLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PriceChangeLog query()
 * @method static \Illuminate\Database\Eloquent\Builder|PriceChangeLog whereBookId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PriceChangeLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PriceChangeLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PriceChangeLog wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PriceChangeLog whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class PriceChangeLog extends Model
{
	protected $fillable = [
		'price'
	];

	public function setPriceAttribute($value)
	{
		if (empty($value))
			$this->attributes['price'] = null;
		else
			$this->attributes['price'] = number_format($value, 2, '.', '');
	}

	public function getPriceAttribute($value)
	{
		$integer_value = (integer)$value;

		if ($value == $integer_value)
			return $integer_value;
		else
			return (float)$value;
	}
}
