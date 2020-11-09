<?php

namespace App;

use App\Model as Model;
use Litlife\Url\Url;

/**
 * App\UrlShort
 *
 * @property int $id
 * @property string $key Уникальный ключ
 * @property string $url Ссылка на которую происходит переход
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|UrlShort newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UrlShort newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByField($column, $ids)
 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
 * @method static \Illuminate\Database\Eloquent\Builder|UrlShort query()
 * @method static \Illuminate\Database\Eloquent\Builder|Model void()
 * @method static \Illuminate\Database\Eloquent\Builder|UrlShort whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UrlShort whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UrlShort whereKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UrlShort whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UrlShort whereUrl($value)
 * @mixin \Eloquent
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
