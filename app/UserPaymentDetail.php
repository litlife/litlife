<?php

namespace App;

use Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;
use Litlife\Unitpay\Facades\UnitPay;
use Propaganistas\LaravelPhone\PhoneNumber;

/**
 * App\UserPaymentDetail
 *
 * @property int $id
 * @property int $user_id ID пользователя которму принадлежат платежные данные
 * @property string $type Тип платежной системы
 * @property string $number Номер кошелька или карты
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property object|null $params Дополнительная информация о платежных данных
 * @property mixed $qiwi
 * @property-read \App\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|UserPaymentDetail newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserPaymentDetail newQuery()
 * @method static Builder|UserPaymentDetail onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByField($column, $ids)
 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
 * @method static \Illuminate\Database\Eloquent\Builder|UserPaymentDetail query()
 * @method static \Illuminate\Database\Eloquent\Builder|Model void()
 * @method static \Illuminate\Database\Eloquent\Builder|UserPaymentDetail whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserPaymentDetail whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserPaymentDetail whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserPaymentDetail whereNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserPaymentDetail whereParams($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserPaymentDetail whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserPaymentDetail whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserPaymentDetail whereUserId($value)
 * @method static Builder|UserPaymentDetail withTrashed()
 * @method static Builder|UserPaymentDetail withoutTrashed()
 * @mixin Eloquent
 */
class UserPaymentDetail extends Model
{
	use SoftDeletes;
	/*
	public $attributes = [
		'card_number' => '',
		'wmr' => '',
		'yandex' => '',
		'qiwi' => ''
	];
 */
	public $fillable = [
		'type',
		'number'
	];

	public $casts = [
		'params' => 'object'
	];

	public $primaryKey = 'id';

	public function setQiwiAttribute($value)
	{
		$this->attributes['qiwi'] = mb_substr($value, 1);
	}

	public function getQiwiAttribute($value)
	{
		if (!empty($value))
			return '+' . $value;
	}

	public function setNumberAttribute($value)
	{
		if ($this->isQiwi())
			$value = PhoneNumber::make($value);

		$this->attributes['number'] = $value;
	}

	public function isQiwi()
	{
		return $this->type == 'qiwi';
	}

	public function user()
	{
		return $this->belongsTo('App\User');
	}

	public function isWebmoney()
	{
		return $this->type == 'webmoney';
	}

	public function isYandex()
	{
		return $this->type == 'yandex';
	}

	public function getComission()
	{
		$type = $this->type;

		if ($this->isCard()) {
			if ($this->isRuCard())
				$type = 'card_rf';
			else
				$type = 'card_not_rf';
		}

		return config('unitpay.withdrawal_restrictions.' . $type . '.comission');
	}

	public function isCard()
	{
		return $this->type == 'card';
	}

	public function isRuCard()
	{
		if (!$this->isCard())
			return false;

		return $this->getCountryCode() == 'RU';
	}

	public function getCountryCode()
	{
		return optional($this->params)->countryCode;
	}

	public function getMinComissionSum()
	{
		$type = $this->type;

		if ($this->isCard()) {
			if ($this->isRuCard())
				$type = 'card_rf';
			else
				$type = 'card_not_rf';
		}

		return config('unitpay.withdrawal_restrictions.' . $type . '.min_comission');
	}

	public function getMin()
	{
		$type = $this->type;

		if ($this->isCard()) {
			if ($this->isRuCard())
				$type = 'card_rf';
			else
				$type = 'card_not_rf';
		}

		return config('unitpay.withdrawal_restrictions.' . $type . '.min');
	}

	public function getMax()
	{
		$type = $this->type;

		if ($this->isCard()) {
			if ($this->isRuCard())
				$type = 'card_rf';
			else
				$type = 'card_not_rf';
		}

		return config('unitpay.withdrawal_restrictions.' . $type . '.max');
	}

	public function getMaxInMonth()
	{
		$type = $this->type;

		if ($this->isCard()) {
			if ($this->isRuCard())
				$type = 'card_rf';
			else
				$type = 'card_not_rf';
		}

		return config('unitpay.withdrawal_restrictions.' . $type . '.max_in_month');
	}

	public function getMaxInDay()
	{
		$type = $this->type;

		if ($this->isCard()) {
			if ($this->isRuCard())
				$type = 'card_rf';
			else
				$type = 'card_not_rf';
		}

		return config('unitpay.withdrawal_restrictions.' . $type . '.max_in_day');
	}

	public function getCardBrand()
	{
		return optional($this->params)->brand;
	}

	public function updateCardInfo()
	{
		if ($this->isCard()) {
			$result = UnitPay::getBinInfo(['bin' => mb_substr($this->number, 0, 6)])
				->request();

			if ($result->isSuccess()) {
				$this->params = $result->result();
			}
		}
	}

	/*
		public function getFilledWallets()
		{
			$array = [];

			foreach ($this->getFillable() as $type)
			{
				if (!empty($this->$type))
				{
					$array[$type] = $this->$type;
				}
			}

			return $array;
		}
		*/
}
