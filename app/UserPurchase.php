<?php

namespace App;

use App\Enums\TransactionType;
use App\Model as Model;
use Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;

/**
 * App\UserPurchase
 *
 * @property int $id
 * @property int $buyer_user_id Аккаунт пользователя, который оплачивает
 * @property int $seller_user_id Аккаунт пользователя, который получает выплату
 * @property string $purchasable_type Тип объекта за который происходит оплата
 * @property int $purchasable_id ID объекта за который происходит оплата
 * @property float $price Цена по которой куплен объект
 * @property int $site_commission Комиссия сайта
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property Carbon|null $canceled_at Время отмены покупки
 * @property-read \App\User $buyer
 * @property-read \App\UserPaymentTransaction|null $buyer_transaction
 * @property-read \App\UserPaymentTransaction|null $commission_transaction
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $purchasable
 * @property-read \App\UserPaymentTransaction|null $referer_buyer_transaction
 * @property-read \App\UserPaymentTransaction|null $referer_seller_transaction
 * @property-read \App\User $seller
 * @property-read \App\UserPaymentTransaction|null $seller_transaction
 * @property-read \App\UserPaymentTransaction|null $transaction
 * @method static \Illuminate\Database\Eloquent\Builder|UserPurchase newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserPurchase newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserPurchase notCanceled()
 * @method static Builder|UserPurchase onlyTrashed()
 * @method static Builder|Model orderByField($column, $ids)
 * @method static Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
 * @method static \Illuminate\Database\Eloquent\Builder|UserPurchase query()
 * @method static Builder|Model void()
 * @method static \Illuminate\Database\Eloquent\Builder|UserPurchase whereBuyerUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserPurchase whereCanceledAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserPurchase whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserPurchase whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserPurchase whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserPurchase wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserPurchase wherePurchasableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserPurchase wherePurchasableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserPurchase whereSellerUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserPurchase whereSiteCommission($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserPurchase whereUpdatedAt($value)
 * @method static Builder|UserPurchase withTrashed()
 * @method static Builder|UserPurchase withoutTrashed()
 * @mixin Eloquent
 */
class UserPurchase extends Model
{
	use SoftDeletes;

	protected $dates = [
		'created_at',
		'updated_at',
		'deleted_at',
		'canceled_at'
	];

	public function buyer()
	{
		return $this->belongsTo('App\User', 'buyer_user_id');
	}

	public function seller()
	{
		return $this->belongsTo('App\User', 'seller_user_id');
	}

	public function purchasable()
	{
		return $this->morphTo();
	}

	public function transaction()
	{
		return $this->morphOne('App\UserPaymentTransaction', 'operable');
	}

	public function buyer_transaction()
	{
		return $this->morphOne('App\UserPaymentTransaction', 'operable')
			->where('type', TransactionType::buy);
	}

	public function seller_transaction()
	{
		return $this->morphOne('App\UserPaymentTransaction', 'operable')
			->where('type', TransactionType::sell);
	}

	public function commission_transaction()
	{
		return $this->morphOne('App\UserPaymentTransaction', 'operable')
			->where('type', TransactionType::comission);
	}

	public function referer_buyer_transaction()
	{
		return $this->morphOne('App\UserPaymentTransaction', 'operable')
			->where('type', TransactionType::comission_referer_buyer);
	}

	public function referer_seller_transaction()
	{
		return $this->morphOne('App\UserPaymentTransaction', 'operable')
			->where('type', TransactionType::comission_referer_seller);
	}

	public function isBook()
	{
		if ($this->purchasable_type == 'book')
			return true;
		else
			return false;
	}

	public function cancel()
	{
		$this->canceled_at = now();
	}

	public function isCanceled(): bool
	{
		return (bool)$this->canceled_at;
	}

	public function scopeNotCanceled($query)
	{
		return $query->whereNull('canceled_at');
	}
}
