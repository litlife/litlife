<?php

namespace App\Traits;

use GeneaLabs\LaravelModelCaching\Traits\Cachable as LaravelModelCachingCachable;

trait Cachable
{
	use LaravelModelCachingCachable;

	public function __construct($attributes = [])
	{
		config(['laravel-model-caching.cache-prefix' => config('app.name')]);

		parent::__construct($attributes);
	}


}