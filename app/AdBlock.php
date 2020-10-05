<?php

namespace App;

use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Model;

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
