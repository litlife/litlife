<?php

namespace App;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Carbon;

/**
 * App\UserGenreBlacklist
 *
 * @property int $user_id
 * @property int $genre_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|UserGenreBlacklist newModelQuery()
 * @method static Builder|UserGenreBlacklist newQuery()
 * @method static Builder|UserGenreBlacklist query()
 * @method static Builder|UserGenreBlacklist whereCreatedAt($value)
 * @method static Builder|UserGenreBlacklist whereGenreId($value)
 * @method static Builder|UserGenreBlacklist whereUpdatedAt($value)
 * @method static Builder|UserGenreBlacklist whereUserId($value)
 * @mixin Eloquent
 */
class UserGenreBlacklist extends Pivot
{
	//
}
