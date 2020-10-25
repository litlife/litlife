<?php

namespace App;

use App\Traits\CheckedItems;
use App\Traits\UserCreate;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SupportRequest extends Model
{
	use UserCreate;
	use CheckedItems;
	use SoftDeletes;

	public function messages()
	{
		return $this->hasMany('App\SupportRequestMessage');
	}
}
