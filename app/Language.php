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
 * @property-read \Illuminate\Database\Eloquent\Collection|Book[] $books
 * @method static \Illuminate\Database\Eloquent\Builder|Language disableCache()
 * @method static CachedBuilder|Language newModelQuery()
 * @method static CachedBuilder|Language newQuery()
 * @method static Builder|Language onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByField($column, $ids)
 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
 * @method static CachedBuilder|Language query()
 * @method static \Illuminate\Database\Eloquent\Builder|Model void()
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
