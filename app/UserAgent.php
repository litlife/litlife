<?php

namespace App;

use App\Model as Model;
use Browser;
use Eloquent;
use hisorange\BrowserDetect\Result;
use Illuminate\Database\Eloquent\Builder;

/**
 * App\UserAgent
 *
 * @property int $id
 * @property string $value
 * @property-read Result $parsed
 * @method static Builder|UserAgent newModelQuery()
 * @method static Builder|UserAgent newQuery()
 * @method static Builder|Model orderByField($column, $ids)
 * @method static Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
 * @method static Builder|UserAgent query()
 * @method static Builder|Model void()
 * @method static Builder|UserAgent whereId($value)
 * @method static Builder|UserAgent whereValue($value)
 * @mixin Eloquent
 */
class UserAgent extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'value'
    ];

    static function getCurrentId()
    {
        $id = ignoreDuplicateException(function () {
            $id = UserAgent::firstOrCreate(['value' => Browser::userAgent()])->id;
            return $id;
        });

        return $id;
    }

    public function setValueAttribute($value)
    {
        $this->attributes['value'] = mb_substr($value, 0, 255);
    }

    public function getParsedAttribute(): Result
    {
        return Browser::parse($this->value);
    }
}
