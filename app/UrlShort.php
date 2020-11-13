<?php

namespace App;

use App\Model as Model;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Litlife\Url\Url;

/**
 * App\UrlShort
 *
 * @property int $id
 * @property string $key Уникальный ключ
 * @property string $url Ссылка на которую происходит переход
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|UrlShort newModelQuery()
 * @method static Builder|UrlShort newQuery()
 * @method static Builder|Model orderByField($column, $ids)
 * @method static Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
 * @method static Builder|UrlShort query()
 * @method static Builder|Model void()
 * @method static Builder|UrlShort whereCreatedAt($value)
 * @method static Builder|UrlShort whereId($value)
 * @method static Builder|UrlShort whereKey($value)
 * @method static Builder|UrlShort whereUpdatedAt($value)
 * @method static Builder|UrlShort whereUrl($value)
 * @mixin Eloquent
 */
class UrlShort extends Model
{
    public $characters = '0123456789bcdfghjklmnpqrstvwxyzBCDFGHJKLMNPQRSTVWXYZ';
    protected $fillable = [
        'key',
        'url'
    ];

    public static function boot()
    {
        static::Creating(function ($model) {

            $id = UrlShort::max('id') ?? 0;

            $model->key = $model->encode($id + 1);
        });

        parent::boot();
    }

    static function init(string $url)
    {
        $url = (string)Url::fromString($url)
            ->getPathQueryFragment();

        return self::firstOrCreate(['url' => $url]);
    }

    public function setUrlAttribute($url)
    {
        $this->attributes['url'] = $url;
    }

    public function getShortUrl()
    {
        return route('url.shortener', ['key' => $this->attributes['key']]);
    }

    public function getFullUrl()
    {
        return (string)Url::fromString(config('app.url') . $this->attributes['url']);
    }

    public function encode(int $integer): string // Digital number  -->>  alphabet letter code
    {
        $base = mb_strlen($this->characters);

        $out = "";

        for ($t = floor(log10($integer) / log10($base)); $t >= 0; $t--) {
            $a = floor($integer / bcpow($base, $t));
            $out = $out . mb_substr($this->characters, $a, 1);
            $integer = $integer - ($a * bcpow($base, $t));
        }

        $out = mb_strrev($out); // reverse

        return $out;
    }

    public function decode(string $code): int // Digital number  <<--  alphabet letter code
    {
        $base = mb_strlen($this->characters);

        $code = mb_strrev($code);
        $out = 0;
        $length = mb_strlen($code) - 1;

        for ($t = 0; $t <= $length; $t++) {
            $bcpow = bcpow($base, $length - $t);
            $out = $out + mb_strpos($this->characters, mb_substr($code, $t, 1)) * $bcpow;
        }

        return $out;
    }
}
