<?php

namespace App;

use App\Enums\PaymentSystemType;
use App\Enums\TransactionType;
use App\Model as Model;

/**
 * App\PaymentSystemComission
 *
 * @property int $id
 * @property string $payment_aggregator
 * @property string $payment_system_type
 * @property int $transaction_type
 * @property float $comission
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentSystemComission deposit()
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentSystemComission lowerComissionFirst()
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentSystemComission newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentSystemComission newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByField($column, $ids)
 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentSystemComission paymentSystemType($number)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentSystemComission query()
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentSystemComission unitPay()
 * @method static \Illuminate\Database\Eloquent\Builder|Model void()
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentSystemComission whereComission($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentSystemComission whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentSystemComission whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentSystemComission whereInPaymentSystemType($array)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentSystemComission wherePaymentAggregator($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentSystemComission wherePaymentSystemType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentSystemComission whereTransactionType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PaymentSystemComission whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class PaymentSystemComission extends Model
{
	public $fillable = [
		'payment_aggregator',
		'payment_system_type',
		'transaction_type',
		'comission'
	];

	public function scopeUnitPay($query)
	{
		return $query->where('payment_aggregator', 'unitpay');
	}

	public function scopeDeposit($query)
	{
		return $query->where('transaction_type', TransactionType::deposit);
	}

	public function scopePaymentSystemType($query, $number)
	{
		return $query->where('payment_system_type', $number);
	}

	public function getPaymentSystemTypeAttribute($value)
	{
		return PaymentSystemType::getKey(intval($value));
	}

	public function scopeLowerComissionFirst($query)
	{
		return $query->orderBy('comission', 'asc');
	}

	public function scopeWhereInPaymentSystemType($query, $array)
	{
		foreach ($array as $key => $item) {
			$array[] = PaymentSystemType::getValue($item);
		}

		return $query->whereIn('payment_system_type', $array);
	}
}
