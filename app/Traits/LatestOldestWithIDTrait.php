<?php

namespace App\Traits;

trait LatestOldestWithIDTrait
{
	public function scopeLatestWithId($query, $column = 'created_at')
	{
		return $query->orderBy($column, 'desc')
			->orderBy('id', 'desc');
	}

	public function scopeOldestWithId($query, $column = 'created_at')
	{
		return $query->orderBy($column, 'asc')
			->orderBy('id', 'asc');
	}
}