<?php

namespace App;

use App\Traits\UserCreate;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SupportRequestMessage extends Model
{
	use UserCreate;
	use SoftDeletes;

	protected $fillable = [
		'text'
	];

	public function supportRequest()
	{
		return $this->belongsTo('App\SupportRequest');
	}

	public function getAnchorId()
	{
		return 'message_id' . $this->id;
	}
}
