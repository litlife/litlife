<?php

namespace App;

use Eloquent;
use GeneaLabs\LaravelModelCaching\CachedBuilder;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\AdBlock
 *
 * @property int $id
 * @property string $name Имя
 * @property string $code Код
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property bool $enabled Включен или нет блок
 * @property string|null $description Описание
 * @property string|null $user_updated_at
 * @method static Builder|AdBlock disableCache()
 * @method static Builder|AdBlock enabled()
 * @method static Builder|AdBlock name($name)
 * @method static CachedBuilder|AdBlock newModelQuery()
 * @method static CachedBuilder|AdBlock newQuery()
 * @method static CachedBuilder|AdBlock query()
 * @method static Builder|AdBlock whereCode($value)
 * @method static Builder|AdBlock whereCreatedAt($value)
 * @method static Builder|AdBlock whereDescription($value)
 * @method static Builder|AdBlock whereEnabled($value)
 * @method static Builder|AdBlock whereId($value)
 * @method static Builder|AdBlock whereName($value)
 * @method static Builder|AdBlock whereUpdatedAt($value)
 * @method static Builder|AdBlock whereUserUpdatedAt($value)
 * @method static Builder|AdBlock withCacheCooldownSeconds($seconds = null)
 * @mixin Eloquent
 */
class AdBlock extends Model
{
    use Cachable;

    public $attributes = [
        'enabled' => false
    ];
    protected $fillable = [
        'name',
        'code',
        'description'
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
