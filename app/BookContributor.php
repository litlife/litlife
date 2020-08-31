<?php

namespace App;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * App\BookContributor
 *
 * @method static Builder|BookContributor newModelQuery()
 * @method static Builder|BookContributor newQuery()
 * @method static Builder|BookContributor query()
 * @mixin Eloquent
 */
class BookContributor extends Pivot
{
	public $table = 'book_contributors';

	public $timestamps = true;
}
