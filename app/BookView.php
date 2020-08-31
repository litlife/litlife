<?php

namespace App;

use App\Model as Model;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;

/**
 * App\BookView
 *
 * @property int $book_id
 * @property int|null $user_id
 * @property int $time
 * @property string|null $ip
 * @method static Builder|BookView newModelQuery()
 * @method static Builder|BookView newQuery()
 * @method static Builder|Model orderByField($column, $ids)
 * @method static Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
 * @method static Builder|BookView query()
 * @method static Builder|Model void()
 * @method static Builder|BookView whereBookId($value)
 * @method static Builder|BookView whereIp($value)
 * @method static Builder|BookView whereTime($value)
 * @method static Builder|BookView whereUserId($value)
 * @mixin Eloquent
 */
class BookView extends Model
{
	//
}
