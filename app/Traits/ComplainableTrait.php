<?php

namespace App\Traits;

trait ComplainableTrait
{
	public function complaints()
	{
		return $this->morphMany('App\Complain', 'complainable');
	}
}