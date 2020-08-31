<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\SearchQueriesLog
 *
 * @property int $id
 * @property string $query_text
 * @property int|null $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|SearchQueriesLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SearchQueriesLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SearchQueriesLog query()
 * @method static \Illuminate\Database\Eloquent\Builder|SearchQueriesLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SearchQueriesLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SearchQueriesLog whereQueryText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SearchQueriesLog whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SearchQueriesLog whereUserId($value)
 * @mixin \Eloquent
 */
class SearchQueriesLog extends Model
{
	protected $fillable = [
		'query_text',
		'user_id'
	];
}
