<?php

namespace App;

use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Model;

class AdBlock extends Model
{
	use Cachable;

	protected $fillable = [
		'name',
		'code'
	];

	public function scopeName($query, $name)
	{
		return $query->where('name', $name);
	}
}
