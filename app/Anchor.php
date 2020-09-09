<?php

namespace App;

use App\Model as Model;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;

/**
 * App\Anchor
 *
 * @method static Builder|Anchor newModelQuery()
 * @method static Builder|Anchor newQuery()
 * @method static Builder|Model orderByField($column, $ids)
 * @method static Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
 * @method static Builder|Anchor query()
 * @method static Builder|Model void()
 * @mixin Eloquent
 */
class Anchor extends Model
{

	public $timestamps = false;

	/*
		protected $guarded = ['sg_id'];

		protected $primaryKey = 'sg_id';

		protected $table = 'subgenre';

		public function books()
		{
			return $this->hasMany('App\Book');
		}

		public function scopeSearch($query, $searchText)
		{
			if ($searchText) {
				//$query->where('sg_name', '~*', "'^".preg_quote($searchText)."'");

				//$query->where('sg_name', 'ilike', $searchText.'%');

				$query->whereRaw('"sg_name" ~* ' . "'" . preg_quote($searchText) . "'");
			}

			return $query;
		}
		*/


}
