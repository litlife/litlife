<?php

namespace App;

use App\Enums\TransactionType;
use App\Model as Model;
use App\Traits\PaymentsStatuses;
use Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;

/**
 * App\UserPaymentTransaction
 *
 * @property int $id
 * @property float $sum Списание или пополненение на балансе
 * @property int $user_id Аккаунт пользователя
 * @property int $type Тип операции
 * @property int $operable_type ID morph таблицы
 * @property int $operable_id ID в таблице
 * @property int $status Статус платежа
 * @property Carbon $status_changed_at Дата изменения статуса платежа
 * @property float|null $balance_before Баланс до проведения операции
 * @property object|null $params Дополнительные данные
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read mixed $balance_after
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $operable
 * @property-read \App\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|UserPaymentTransaction deposit()
 * @method static \Illuminate\Database\Eloquent\Builder|UserPaymentTransaction newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserPaymentTransaction newQuery()
 * @method static Builder|UserPaymentTransaction onlyTrashed()
 * @method static Builder|Model orderByField($column, $ids)
 * @method static Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
 * @method static \Illuminate\Database\Eloquent\Builder|UserPaymentTransaction processed()
 * @method static \Illuminate\Database\Eloquent\Builder|UserPaymentTransaction query()
 * @method static Builder|Model void()
 * @method static \Illuminate\Database\Eloquent\Builder|UserPaymentTransaction wait()
 * @method static \Illuminate\Database\Eloquent\Builder|UserPaymentTransaction whereBalanceBefore($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserPaymentTransaction whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserPaymentTransaction whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserPaymentTransaction whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserPaymentTransaction whereOperableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserPaymentTransaction whereOperableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserPaymentTransaction whereParams($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserPaymentTransaction whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserPaymentTransaction whereStatusChangedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserPaymentTransaction whereSum($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserPaymentTransaction whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserPaymentTransaction whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserPaymentTransaction whereUserId($value)
 * @method static Builder|UserPaymentTransaction withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|UserPaymentTransaction withdrawal()
 * @method static Builder|UserPaymentTransaction withoutTrashed()
 * @mixin Eloquent
 */
class UserPaymentTransaction extends Model
{
    use SoftDeletes;
    use PaymentsStatuses;

    public $casts = [
        'params' => 'object'
    ];

    public $dates = [
        'status_changed_at'
    ];

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id', 'id');
    }

    public function operable()
    {
        return $this->morphTo();
    }

    public function getTypeAttribute($value)
    {
        return TransactionType::getKey(intval($value));
    }

    public function setTypeAttribute($value)
    {
        if (!is_integer($value)) {
            $value = TransactionType::getValue(mb_ucfirst($value));
        }

        $this->attributes['type'] = $value;
    }

    public function isSell()
    {
        return $this->type == TransactionType::getKey(TransactionType::sell);
    }

    public function isBuy()
    {
        return $this->type == TransactionType::getKey(TransactionType::buy);
    }

    public function isWithdrawal()
    {
        return $this->type == TransactionType::getKey(TransactionType::withdrawal);
    }

    public function isDeposit()
    {
        return $this->type == TransactionType::getKey(TransactionType::deposit);
    }

    public function isComission()
    {
        return $this->type == TransactionType::getKey(TransactionType::comission);
    }

    public function isReceipt()
    {
        return $this->type == TransactionType::getKey(TransactionType::receipt);
    }

    public function isTransfer()
    {
        return $this->type == TransactionType::getKey(TransactionType::transfer);
    }

    public function isComissionRefererBuyer()
    {
        return $this->type == TransactionType::getKey(TransactionType::comission_referer_buyer);
    }

    public function isComissionRefererSeller()
    {
        return $this->type == TransactionType::getKey(TransactionType::comission_referer_seller);
    }

    public function typeDeposit()
    {
        $this->type = TransactionType::deposit;
    }

    public function typeWithdrawal()
    {
        $this->type = TransactionType::withdrawal;
    }

    public function typeBuy()
    {
        $this->type = TransactionType::buy;
    }

    public function typeSell()
    {
        $this->type = TransactionType::sell;
    }

    public function typeComission()
    {
        $this->type = TransactionType::comission;
    }

    public function typeComissionRefererBuyer()
    {
        $this->type = TransactionType::comission_referer_buyer;
    }

    public function typeComissionRefererSeller()
    {
        $this->type = TransactionType::comission_referer_seller;
    }

    public function typeReceipt()
    {
        $this->type = TransactionType::receipt;
    }

    public function typeTransfer()
    {
        $this->type = TransactionType::transfer;
    }

    public function scopeDeposit($query)
    {
        return $query->where('type', TransactionType::deposit);
    }

    public function scopeWithdrawal($query)
    {
        return $query->where('type', TransactionType::withdrawal);
    }

    public function getBalanceAfterAttribute()
    {
        return $this->attributes['balance_before'] + $this->attributes['sum'];
    }
}
