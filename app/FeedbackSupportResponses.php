<?php

namespace App;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\FeedbackSupportResponses
 *
 * @property int $id
 * @property int $support_question_id ID вопроса в поддержку
 * @property string|null $text Текст отзыва
 * @property int|null $face_reaction Код реакции в виде смайлика
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read SupportQuestion $support_question
 * @method static Builder|FeedbackSupportResponses newModelQuery()
 * @method static Builder|FeedbackSupportResponses newQuery()
 * @method static Builder|FeedbackSupportResponses query()
 * @method static Builder|FeedbackSupportResponses whereCreatedAt($value)
 * @method static Builder|FeedbackSupportResponses whereFaceReaction($value)
 * @method static Builder|FeedbackSupportResponses whereId($value)
 * @method static Builder|FeedbackSupportResponses whereSupportQuestionId($value)
 * @method static Builder|FeedbackSupportResponses whereText($value)
 * @method static Builder|FeedbackSupportResponses whereUpdatedAt($value)
 * @mixin Eloquent
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
