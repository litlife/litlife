<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FeedbackSupportResponses extends Model
{
	public $fillable = [
		'text',
		'face_reaction'
	];

	public function support_question()
	{
		return $this->belongsTo('App\SupportQuestion');
	}
}
