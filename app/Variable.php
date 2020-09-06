<?php

namespace App;

use App\Model as Model;
use Eloquent;
use GeneaLabs\LaravelModelCaching\CachedBuilder;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

/**
 * App\Variable
 *
 * @property string $name
 * @property string|null $value
 * @property int $old_update_time
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $id
 * @method static Builder|Variable disableCache()
 * @method static CachedBuilder|Variable newModelQuery()
 * @method static CachedBuilder|Variable newQuery()
 * @method static Builder|Model orderByField($column, $ids)
 * @method static Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
 * @method static CachedBuilder|Variable query()
 * @method static Builder|Model void()
 * @method static Builder|Variable whereCreatedAt($value)
 * @method static Builder|Variable whereId($value)
 * @method static Builder|Variable whereName($value)
 * @method static Builder|Variable whereOldUpdateTime($value)
 * @method static Builder|Variable whereUpdatedAt($value)
 * @method static Builder|Variable whereValue($value)
 * @method static Builder|Variable withCacheCooldownSeconds($seconds = null)
 * @mixin Eloquent
 */
class Variable extends Model
{
	use Cachable;

	public $rememberCacheTag = 'Variable';
	protected $fillable = [
		'name',
		'value'
	];

	public function getValueAttribute($value)
	{
		return unserialize($value);
	}

	public function setValueAttribute($value)
	{
		$this->attributes['value'] = serialize($value);
	}
}
