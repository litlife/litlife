<?php

namespace App;

use App\Traits\BBCodeable;
use App\Traits\CharactersCountTrait;
use App\Traits\UserCreate;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\SupportQuestionMessage
 *
 * @property int $id
 * @property int $support_question_id Создатель сообщения
 * @property int $create_user_id Создатель сообщения
 * @property string $text Текст сообщения
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\User $create_user
 * @property-read \App\SupportQuestion $supportQuestion
 * @method static \Illuminate\Database\Eloquent\Builder|SupportQuestionMessage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SupportQuestionMessage newQuery()
 * @method static \Illuminate\Database\Query\Builder|SupportQuestionMessage onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|SupportQuestionMessage query()
 * @method static \Illuminate\Database\Eloquent\Builder|SupportQuestionMessage whereCreateUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SupportQuestionMessage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SupportQuestionMessage whereCreator(\App\User $user)
 * @method static \Illuminate\Database\Eloquent\Builder|SupportQuestionMessage whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SupportQuestionMessage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SupportQuestionMessage whereSupportQuestionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SupportQuestionMessage whereText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SupportQuestionMessage whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|SupportQuestionMessage withTrashed()
 * @method static \Illuminate\Database\Query\Builder|SupportQuestionMessage withoutTrashed()
 * @mixin \Eloquent
 */
class SupportQuestionMessage extends Model
{
	use UserCreate;
	use SoftDeletes;
	use BBCodeable;
	use CharactersCountTrait;

	protected $fillable = [
		'bb_text'
	];

	public function supportQuestion()
	{
		return $this->belongsTo('App\SupportQuestion');
	}

	public function getAnchorId()
	{
		return 'message_id' . $this->id;
	}

	public function setBBTextAttribute($value)
	{
		$this->setBBCode($value);
		$this->attributes['external_images_downloaded'] = false;
		$this->refreshCharactersCount();
	}

	public function setHtmlTextAttribute($value)
	{
		$this->setHtml($value);
		$this->attributes['external_images_downloaded'] = false;
		$this->refreshCharactersCount();
	}

	public function getContent(): string
	{
		return $this->text;
	}

	public function getPreviewText($length = 100)
	{
		$text = str_replace("\r\n", '', $this->text);
		$text = str_replace("\n", '', $text);
		$text = preg_replace('/\<blockquote\ class\=\"bb\ bb\_quote\"\>([\\s\\S]*?)\<\/blockquote\>/iu', ' ', $text);

		$text = preg_replace('/\<img(.*)\>/iu', '(' . __('Image') . ')', $text);
		$text = preg_replace('/\<iframe\ ([\\s\\S]*?)>([\\s\\S]*?)\<\/iframe\>/iu', '(' . __('Video') . ')', $text);

		return trim(html_entity_decode(mb_substr(strip_tags($text), 0, $length)));
	}
}
