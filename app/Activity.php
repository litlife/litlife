<?php

namespace App;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * App\Activity
 *
 * @property int $id
 * @property string $description
 * @property int $subject_id
 * @property string $subject_type
 * @property int $causer_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $causer_type
 * @property string|null $log_name
 * @property \Illuminate\Support\Collection|null $properties
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $causer
 * @property-read \Illuminate\Support\Collection $changes
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $subject
 * @method static Builder|Activity causedBy(\Illuminate\Database\Eloquent\Model $causer)
 * @method static Builder|Activity forSubject(\Illuminate\Database\Eloquent\Model $subject)
 * @method static Builder|Activity inLog($logNames)
 * @method static Builder|Activity newModelQuery()
 * @method static Builder|Activity newQuery()
 * @method static Builder|Activity query()
 * @method static Builder|Activity whereCauserId($value)
 * @method static Builder|Activity whereCauserType($value)
 * @method static Builder|Activity whereCreatedAt($value)
 * @method static Builder|Activity whereDescription($value)
 * @method static Builder|Activity whereId($value)
 * @method static Builder|Activity whereLogName($value)
 * @method static Builder|Activity whereProperties($value)
 * @method static Builder|Activity whereSubjectId($value)
 * @method static Builder|Activity whereSubjectType($value)
 * @method static Builder|Activity whereUpdatedAt($value)
 * @mixin Eloquent
 */
class Activity extends \Spatie\Activitylog\Models\Activity
{
    use HasFactory;

    protected $table = 'activity_log';

    public function getChanges()
    {
        if (!isset($this->changes()['attributes']) or !isset($this->changes()['old'])) {
            return [];
        }

        $attributes = $this->changes()['attributes'];
        $old = $this->changes()['old'];

        $array = [];

        foreach ($attributes as $key => $value) {
            $array[$key] = [
                'new' => $attributes[$key],
                'old' => $old[$key]
            ];
        }

        return $array;
    }

    public function getPropertiesAttribute($value) :Collection
    {
        if ($value == null or empty($value))
            return new Collection();

        if (is_string($value) and $value == '[]')
            return new Collection();

        $value = @json_decode($value);

        if (!$value instanceof Collection)
            return new Collection($value);

        return $value;
    }
}
