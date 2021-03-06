<?php


namespace App;

use App\Model as Model;
use Eloquent;
use GeneaLabs\LaravelModelCaching\CachedBuilder;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;

/**
 * App\Language
 *
 * @property int $id
 * @property string $name
 * @property string $code
 * @property int $priority
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Book[] $books
 * @method static \Illuminate\Database\Eloquent\Builder|Language disableCache()
 * @method static CachedBuilder|Language newModelQuery()
 * @method static CachedBuilder|Language newQuery()
 * @method static Builder|Language onlyTrashed()
 * @method static Builder|Model orderByField($column, $ids)
 * @method static Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
 * @method static CachedBuilder|Language query()
 * @method static Builder|Model void()
 * @method static \Illuminate\Database\Eloquent\Builder|Language whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Language whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Language whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Language whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Language whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Language wherePriority($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Language whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Language withCacheCooldownSeconds($seconds = null)
 * @method static Builder|Language withTrashed()
 * @method static Builder|Language withoutTrashed()
 * @mixin Eloquent
 * @method static CachedBuilder|Language all($columns = [])
 * @method static CachedBuilder|Language avg($column)
 * @method static CachedBuilder|Language cache(array $tags = [])
 * @method static CachedBuilder|Language cachedValue(array $arguments, string $cacheKey)
 * @method static CachedBuilder|Language count($columns = '*')
 * @method static CachedBuilder|Language disableModelCaching()
 * @method static CachedBuilder|Language flushCache(array $tags = [])
 * @method static CachedBuilder|Language getModelCacheCooldown(\Illuminate\Database\Eloquent\Model $instance)
 * @method static CachedBuilder|Language inRandomOrder($seed = '')
 * @method static CachedBuilder|Language insert(array $values)
 * @method static CachedBuilder|Language isCachable()
 * @method static CachedBuilder|Language max($column)
 * @method static CachedBuilder|Language min($column)
 * @method static CachedBuilder|Language sum($column)
 * @method static CachedBuilder|Language truncate()
 */
class Language extends Model
{
    use SoftDeletes;
    use Cachable;

    public function books()
    {
        return $this->hasMany('App\Book', 'ti_lb', 'code');
    }
}
