<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\FeedbackSupportResponses
 *
 * @property int $id
 * @property int $support_question_id ID вопроса в поддержку
 * @property string|null $text Текст отзыва
 * @property int|null $face_reaction Код реакции в виде смайлика
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\SupportQuestion $support_question
 * @method static \Illuminate\Database\Eloquent\Builder|FeedbackSupportResponses newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FeedbackSupportResponses newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FeedbackSupportResponses query()
 * @method static \Illuminate\Database\Eloquent\Builder|FeedbackSupportResponses whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FeedbackSupportResponses whereFaceReaction($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FeedbackSupportResponses whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FeedbackSupportResponses whereSupportQuestionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FeedbackSupportResponses whereText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FeedbackSupportResponses whereUpdatedAt($value)
 * @mixin \Eloquent
 */
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
