<?php

namespace App;

use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Model;

/**
 * App\AdBlock
 *
 * @property int $id
 * @property string $name Имя
 * @property string $code Код
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property bool $enabled Включен или нет блок
 * @property string|null $description Описание
 * @property string|null $user_updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|AdBlock disableCache()
 * @method static \Illuminate\Database\Eloquent\Builder|AdBlock enabled()
 * @method static \Illuminate\Database\Eloquent\Builder|AdBlock name($name)
 * @method static \GeneaLabs\LaravelModelCaching\CachedBuilder|AdBlock newModelQuery()
 * @method static \GeneaLabs\LaravelModelCaching\CachedBuilder|AdBlock newQuery()
 * @method static \GeneaLabs\LaravelModelCaching\CachedBuilder|AdBlock query()
 * @method static \Illuminate\Database\Eloquent\Builder|AdBlock whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdBlock whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdBlock whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdBlock whereEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdBlock whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdBlock whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdBlock whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdBlock whereUserUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdBlock withCacheCooldownSeconds($seconds = null)
 * @mixin \Eloquent
 */
class AdBlock extends Model
{
	use Cachable;

	protected $fillable = [
		'name',
		'code',
		'description'
	];

	public $attributes = [
		'enabled' => false
	];

	public function scopeName($query, $name)
	{
		return $query->where('name', $name);
	}

	public function scopeEnabled($query)
	{
		return $query->where('enabled', true);
	}

	public function enable()
	{
		$this->enabled = true;
	}

	public function disable()
	{
		$this->enabled = false;
	}

	public function isEnabled(): bool
	{
		return (bool)$this->enabled;
	}
}
