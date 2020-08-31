<?php

namespace App\Policies;

use App\User;
use App\UserPaymentTransaction;

class UserPaymentTransactionPolicy extends Policy
{


	/**
	 * Может ли пользователь отменить транзакцию
	 *
	 * @param User $auth_user
	 * @param UserPaymentTransaction $transaction
	 * @return bool
	 */
	public function cancel(User $auth_user, UserPaymentTransaction $transaction)
	{
		if (!$transaction->isStatusWait())
			return false;

		if ($transaction->user_id != $auth_user->id)
			return false;

		return true;
	}

	/**
	 * Может ли пользователь оплатить существующую транзакцию
	 *
	 * @param User $auth_user
	 * @param UserPaymentTransaction $transaction
	 * @return bool
	 */
	public function pay(User $auth_user, UserPaymentTransaction $transaction)
	{
		if (!$transaction->isDeposit())
			return false;

		if (!$transaction->isStatusWait()
			and !$transaction->isStatusProcessing()
			and !$transaction->isStatusError())
			return false;

		if ($transaction->user_id != $auth_user->id)
			return false;

		return true;
	}
}
