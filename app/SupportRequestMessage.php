<?php

namespace App;

use App\Traits\UserCreate;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\SupportRequestMessage
 *
 * @property int $id
 * @property int $support_request_id Создатель сообщения
 * @property int $create_user_id Создатель сообщения
 * @property string $text Текст сообщения
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\User $create_user
 * @property-read \App\SupportRequest $supportRequest
 * @method static \Illuminate\Database\Eloquent\Builder|SupportRequestMessage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SupportRequestMessage newQuery()
 * @method static \Illuminate\Database\Query\Builder|SupportRequestMessage onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|SupportRequestMessage query()
 * @method static \Illuminate\Database\Eloquent\Builder|SupportRequestMessage whereCreateUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SupportRequestMessage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SupportRequestMessage whereCreator(\App\User $user)
 * @method static \Illuminate\Database\Eloquent\Builder|SupportRequestMessage whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SupportRequestMessage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SupportRequestMessage whereSupportRequestId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SupportRequestMessage whereText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SupportRequestMessage whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|SupportRequestMessage withTrashed()
 * @method static \Illuminate\Database\Query\Builder|SupportRequestMessage withoutTrashed()
 * @mixin \Eloquent
 */
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
