<?php

namespace App;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\SearchQueriesLog
 *
 * @property int $id
 * @property string $query_text
 * @property int|null $user_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|SearchQueriesLog newModelQuery()
 * @method static Builder|SearchQueriesLog newQuery()
 * @method static Builder|SearchQueriesLog query()
 * @method static Builder|SearchQueriesLog whereCreatedAt($value)
 * @method static Builder|SearchQueriesLog whereId($value)
 * @method static Builder|SearchQueriesLog whereQueryText($value)
 * @method static Builder|SearchQueriesLog whereUpdatedAt($value)
 * @method static Builder|SearchQueriesLog whereUserId($value)
 * @mixin Eloquent
 */
class SearchQueriesLog extends Model
{
    protected $fillable = [
        'query_text',
        'user_id'
    ];
}
