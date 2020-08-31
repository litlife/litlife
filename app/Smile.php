<?php

namespace App;

use App\Traits\Cachable;
use App\Traits\ImageResizable;
use Eloquent;
use GeneaLabs\LaravelModelCaching\CachedBuilder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;

/**
 * App\Smile
 *
 * @property int $id
 * @property string $name
 * @property string $description
 * @property string|null $simple_form
 * @property string|null $for
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property object|null $parameters
 * @property string $storage
 * @property string|null $dirname
 * @property-read mixed $full_url200x200
 * @property-read mixed $full_url50x50
 * @property-read mixed $full_url90x90
 * @property-read mixed $full_url
 * @property-read mixed $url
 * @property-read mixed $full_url_sized
 * @property-write mixed $max_height
 * @property-write mixed $max_width
 * @property-write mixed $quality
 * @method static \Illuminate\Database\Eloquent\Builder|Smile considerTime()
 * @method static \Illuminate\Database\Eloquent\Builder|Smile disableCache()
 * @method static CachedBuilder|Smile newModelQuery()
 * @method static CachedBuilder|Smile newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Smile newYear()
 * @method static Builder|Smile onlyTrashed()
 * @method static Builder|Model orderByField($column, $ids)
 * @method static Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
 * @method static CachedBuilder|Smile query()
 * @method static \Illuminate\Database\Eloquent\Builder|Smile regular()
 * @method static Builder|Model void()
 * @method static \Illuminate\Database\Eloquent\Builder|Smile whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Smile whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Smile whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Smile whereDirname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Smile whereFor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Smile whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Smile whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Smile whereParameters($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Smile whereSimpleForm($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Smile whereStorage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Smile whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Smile withCacheCooldownSeconds($seconds = null)
 * @method static Builder|Smile withTrashed()
 * @method static Builder|Smile withoutTrashed()
 * @mixin Eloquent
 */
class Smile extends Model
{
	use SoftDeletes;
	use ImageResizable;
	use Cachable;

	protected $casts = [
		'parameters' => 'object'
	];

	public function scopeRegular($query)
	{
		return $query->whereNull("for");
	}

	public function scopeNewYear($query)
	{
		return $query->orderByField("for", ['NewYear']);
	}

	/*
		public function scopeRemember($query, $minutes)
		{
			return Cache::remember($query->toSql(), $minutes, function () use ($query) {
				return $query->get();
			});
		}
		*/
	/*
	public function getUrlAttribute()
	{
		return '/images/smiles/' . $this->name;
	}
 */

	public function getSimpleFormAttribute($value)
	{
		if (empty($value))
			return ':' . $this->description . ':';
		else
			return $value;
	}

	public function getWidth()
	{
		return $this->parameters->width;
	}

	public function getHeight()
	{
		return $this->parameters->height;
	}

	public function freshImageSize()
	{
		$object = (object)$this->parameters;
		$object->width = $this->getRealWidth();
		$object->height = $this->getRealHeight();
		$this->parameters = $object;
		$this->save();
	}

	public function scopeConsiderTime($query)
	{
		if ($this->isIncludeSmilesForNewYear())
			return $query->newYear();
		else
			return $query->regular();
	}

	public function isIncludeSmilesForNewYear()
	{
		if (now()->month == 12)
			if (now()->day >= 10)
				return true;

		if (now()->month == 1)
			if (now()->day <= 15)
				return true;

		return false;
	}
}
