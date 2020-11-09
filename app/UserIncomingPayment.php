<?php

namespace App;

use App\Model as Model;
use Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;

/**
 * App\UserIncomingPayment
 *
 * @property int $id
 * @property string $payment_type Код платежной системы
 * @property int $user_id Аккаунт пользователя на который зачисляется платеж
 * @property string $ip IP с которого осуществляется платеж
 * @property string $currency Код валюты
 * @property int|null $payment_id ID транзакции внутри платежного агрегатора
 * @property string $payment_aggregator Название платежного агрегатора приема платежей
 * @property object|null $params Все данные платежа
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read \App\UserPaymentTransaction|null $transaction
 * @property-read \App\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|UserIncomingPayment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserIncomingPayment newQuery()
 * @method static Builder|UserIncomingPayment onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByField($column, $ids)
 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
 * @method static \Illuminate\Database\Eloquent\Builder|UserIncomingPayment query()
 * @method static \Illuminate\Database\Eloquent\Builder|UserIncomingPayment unitPay()
 * @method static \Illuminate\Database\Eloquent\Builder|UserIncomingPayment unitPayPayment($id)
 * @method static \Illuminate\Database\Eloquent\Builder|Model void()
 * @method static \Illuminate\Database\Eloquent\Builder|UserIncomingPayment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserIncomingPayment whereCurrency($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserIncomingPayment whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserIncomingPayment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserIncomingPayment whereIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserIncomingPayment whereParams($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserIncomingPayment wherePaymentAggregator($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserIncomingPayment wherePaymentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserIncomingPayment wherePaymentType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserIncomingPayment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserIncomingPayment whereUserId($value)
 * @method static Builder|UserIncomingPayment withTrashed()
 * @method static Builder|UserIncomingPayment withoutTrashed()
 * @mixin Eloquent
 */
class UserIncomingPayment extends Model
{
	use SoftDeletes;

	public $casts = [
		'params' => 'object'
	];

	public function user()
	{
		return $this->belongsTo('App\User', 'user_id', 'id');
	}

	public function transaction()
	{
		return $this->morphOne('App\UserPaymentTransaction', 'operable');
	}

	public function scopeUnitPayPayment($query, $id)
	{
		return $query->where('payment_aggregator', 'unitpay')
			->where('payment_id', $id);
	}

	public function scopeUnitPay($query)
	{
		return $query->where('payment_aggregator', 'unitpay');
	}

	public function getPaymentError()
	{
		return optional($this->params->result)->errorMessage;
	}

	public function getErrorCode()
	{
		return $this->params->error->code;
	}

	public function getParamsArray()
	{
		return json_decode(json_encode($this->params), true);
	}

	public function setPaymentTypeAttribute($value)
	{
		$value = mb_strtolower($value);

		if ($value == 'wmr')
			$value = 'webmoney';

		$this->attributes['payment_type'] = $value;
	}

	public function getPaymentType()
	{
		$type = '';

		if (isset($this->params->result->paymentType))
			$type = $this->params->result->paymentType;

		return $type;
	}

	public function getPurse()
	{
		$purse = '';

		if (isset($this->params->result->purse))
			$purse = $this->params->result->purse;

		return $purse;
	}
}
