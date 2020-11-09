<?php

namespace App;

use App\Model as Model;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;

/**
 * App\BookSimilar
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\BookSimilarVote[] $votes
 * @method static Builder|BookSimilar newModelQuery()
 * @method static Builder|BookSimilar newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByField($column, $ids)
 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
 * @method static Builder|BookSimilar query()
 * @method static \Illuminate\Database\Eloquent\Builder|Model void()
 * @mixin Eloquent
 */
class BookSimilar extends Model
{
	public function votes()
	{
		return $this->hasMany('App\BookSimilarVote');
	}
}
