<?php

namespace App;

use App\Enums\TransactionType;
use App\Model as Model;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

/**
 * App\UserMoneyTransfer
 *
 * @property int $id
 * @property int $sender_user_id
 * @property int $recepient_user_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read \App\User $recepient
 * @property-read \App\UserPaymentTransaction|null $recepient_transaction
 * @property-read \App\User $sender
 * @property-read \App\UserPaymentTransaction|null $sender_transaction
 * @property-read \App\UserPaymentTransaction|null $transaction
 * @method static Builder|UserMoneyTransfer newModelQuery()
 * @method static Builder|UserMoneyTransfer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByField($column, $ids)
 * @method static \Illuminate\Database\Eloquent\Builder|Model orderByWithNulls($column, $sort = 'asc', $nulls = 'first')
 * @method static Builder|UserMoneyTransfer query()
 * @method static \Illuminate\Database\Eloquent\Builder|Model void()
 * @method static Builder|UserMoneyTransfer whereCreatedAt($value)
 * @method static Builder|UserMoneyTransfer whereId($value)
 * @method static Builder|UserMoneyTransfer whereRecepientUserId($value)
 * @method static Builder|UserMoneyTransfer whereSenderUserId($value)
 * @method static Builder|UserMoneyTransfer whereUpdatedAt($value)
 * @mixin Eloquent
 */
class UserMoneyTransfer extends Model
{
	public function sender()
	{
		return $this->belongsTo('App\User', 'sender_user_id');
	}

	public function recepient()
	{
		return $this->belongsTo('App\User', 'recepient_user_id');
	}

	public function transaction()
	{
		return $this->morphOne('App\UserPaymentTransaction', 'operable');
	}

	public function sender_transaction()
	{
		return $this->morphOne('App\UserPaymentTransaction', 'operable')
			->where('type', TransactionType::transfer);
	}

	public function recepient_transaction()
	{
		return $this->morphOne('App\UserPaymentTransaction', 'operable')
			->where('type', TransactionType::receipt);
	}
}
