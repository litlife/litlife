<?php

namespace App;

use App\Model as Model;
use Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * App\UserOutgoingPayment
 *
 * @property int $id
 * @property int $user_id Аккаунт пользователя c которого идет платеж
 * @property string $ip IP пользователя, который заказывает выплату
 * @property string $purse Номер кошелька на который перечисляется выплата
 * @property string $payment_type Тип платежной системы на которую перечисляется выплата
 * @property int $wallet_id ID кошелька для выплаты
 * @property string|null $payment_aggregator Платежный агрегатор через который осуществляется выплата
 * @property int|null $payment_aggregator_transaction_id ID транзакции платежного агрегатора, через который происходит выплата
 * @property object|null $params Данные полученные от платежной системы
 * @property int|null $retry_failed_count Сколько попыток было отправить платеж
 * @property string|null $last_failed_retry_at Время последней попытки отправить платеж
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property string $uniqid Уникальный номер транзакции
 * @property-read UserPaymentTransaction|null $transaction
 * @property-read User $user
 * @method static \Illuminate\Database\Eloquent\Builder|UserOutgoingPayment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserOutgoingPayment newQuery()
 * @method static Builder|UserOutgoingPayment onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByField($column, $ids)
 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
 * @method static \Illuminate\Database\Eloquent\Builder|UserOutgoingPayment query()
 * @method static \Illuminate\Database\Eloquent\Builder|Model void()
 * @method static \Illuminate\Database\Eloquent\Builder|UserOutgoingPayment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserOutgoingPayment whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserOutgoingPayment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserOutgoingPayment whereIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserOutgoingPayment whereLastFailedRetryAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserOutgoingPayment whereParams($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserOutgoingPayment wherePaymentAggregator($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserOutgoingPayment wherePaymentAggregatorTransactionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserOutgoingPayment wherePaymentType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserOutgoingPayment wherePurse($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserOutgoingPayment whereRetryFailedCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserOutgoingPayment whereUniqid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserOutgoingPayment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserOutgoingPayment whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserOutgoingPayment whereWalletId($value)
 * @method static Builder|UserOutgoingPayment withTrashed()
 * @method static Builder|UserOutgoingPayment withoutTrashed()
 * @mixin Eloquent
 */
class UserOutgoingPayment extends Model
{
    use SoftDeletes;

    public $casts = [
        'params' => 'object'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->uniqid = Str::uuid()->toString();
        });
    }

    public function transaction()
    {
        return $this->morphOne('App\UserPaymentTransaction', 'operable');
    }

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id', 'id');
    }

    public function getErrorCode()
    {
        return $this->params->error->code;
    }

    public function getParamsArray()
    {
        return json_decode(json_encode($this->params), true);
    }

    public function getPaymentError()
    {
        return $this->params->error->message;
    }

    public function setPaymentTypeAttribute($value)
    {
        $value = mb_strtolower($value);

        if ($value == 'wmr') {
            $value = 'webmoney';
        }

        $this->attributes['payment_type'] = $value;
    }

    public function getPayoutComission()
    {
        return floatval(optional(optional($this->params)->result)->payoutCommission);
    }

    public function getPartnerComission()
    {
        return floatval(optional(optional($this->params)->result)->partnerCommission);
    }
}
