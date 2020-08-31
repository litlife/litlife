<?php

namespace App;

use App\Enums\UserSubscriptionsEventNotificationType;
use App\Model as Model;
use BenSampo\Enum\Traits\CastsEnums;

/**
 * App\UserSubscriptionsEventNotification
 *
 * @property int $id
 * @property int $notifiable_user_id ID пользователя которому присылаются уведомления
 * @property int $eventable_type Тип объекта при для которго появляется событие
 * @property int $eventable_id ID объекта у которого появляется событие
 * @property int $event_type Тип события
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $eventable
 * @property-read \App\User $notifiable_user
 * @method static \Illuminate\Database\Eloquent\Builder|UserSubscriptionsEventNotification newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserSubscriptionsEventNotification newQuery()
 * @method static Builder|Model orderByField($column, $ids)
 * @method static Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
 * @method static \Illuminate\Database\Eloquent\Builder|UserSubscriptionsEventNotification query()
 * @method static Builder|Model void()
 * @method static \Illuminate\Database\Eloquent\Builder|UserSubscriptionsEventNotification whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserSubscriptionsEventNotification whereEventType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserSubscriptionsEventNotification whereEventableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserSubscriptionsEventNotification whereEventableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserSubscriptionsEventNotification whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserSubscriptionsEventNotification whereNotifiableUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserSubscriptionsEventNotification whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class UserSubscriptionsEventNotification extends Model
{
	use CastsEnums;

	protected $fillable = [
		'notifiable_user_id',
		'eventable_type',
		'eventable_id',
		'event_type'
	];

	protected $visible = [
		'id',
		'notifiable_user_id',
		'eventable_type',
		'eventable_id',
		'event_type',
		'created_at',
		'updated_at'
	];

	protected $enumCasts = [
		// 'attribute_name' => Enum::class
		'event_type' => UserSubscriptionsEventNotificationType::class,
	];

	protected $casts = [
		'event_type' => 'int',
		'eventable_type' => 'int'
	];

	public function notifiable_user()
	{
		return $this->belongsTo('App\User');
	}

	public function eventable()
	{
		return $this->morphTo()->any();
	}
}
