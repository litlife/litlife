<?php

namespace App;

use App\Enums\PaymentSystemType;
use App\Enums\TransactionType;
use App\Model as Model;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

/**
 * App\PaymentSystemComission
 *
 * @property int $id
 * @property string $payment_aggregator
 * @property string $payment_system_type
 * @property int $transaction_type
 * @property float $comission
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|PaymentSystemComission deposit()
 * @method static Builder|PaymentSystemComission lowerComissionFirst()
 * @method static Builder|PaymentSystemComission newModelQuery()
 * @method static Builder|PaymentSystemComission newQuery()
 * @method static Builder|Model orderByField($column, $ids)
 * @method static Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
 * @method static Builder|PaymentSystemComission paymentSystemType($number)
 * @method static Builder|PaymentSystemComission query()
 * @method static Builder|PaymentSystemComission unitPay()
 * @method static Builder|Model void()
 * @method static Builder|PaymentSystemComission whereComission($value)
 * @method static Builder|PaymentSystemComission whereCreatedAt($value)
 * @method static Builder|PaymentSystemComission whereId($value)
 * @method static Builder|PaymentSystemComission whereInPaymentSystemType($array)
 * @method static Builder|PaymentSystemComission wherePaymentAggregator($value)
 * @method static Builder|PaymentSystemComission wherePaymentSystemType($value)
 * @method static Builder|PaymentSystemComission whereTransactionType($value)
 * @method static Builder|PaymentSystemComission whereUpdatedAt($value)
 * @mixin Eloquent
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
