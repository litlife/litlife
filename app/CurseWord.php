<?php

namespace App;

use App\Model as Model;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;

/**
 * App\CurseWord
 *
 * @property int $id
 * @property string $text
 * @method static Builder|CurseWord newModelQuery()
 * @method static Builder|CurseWord newQuery()
 * @method static Builder|Model orderByField($column, $ids)
 * @method static Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
 * @method static Builder|CurseWord query()
 * @method static Builder|Model void()
 * @method static Builder|CurseWord whereId($value)
 * @method static Builder|CurseWord whereText($value)
 * @mixin Eloquent
 */
class CurseWord extends Model
{
	//
}
