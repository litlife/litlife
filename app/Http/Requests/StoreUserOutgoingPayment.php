<?php

namespace App\Http\Requests;

use App\Rules\WithdrawalSum;
use App\UserPaymentDetail;
use Illuminate\Foundation\Http\FormRequest;

class StoreUserOutgoingPayment extends FormRequest
{
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize()
	{
		return true;
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		$rules = [
			'wallet' => ['required', 'integer'],
			'sum' => ['required', 'integer']
		];

		$detail = UserPaymentDetail::findOrFail($this->request->get('wallet'));

		if ($detail->isCard()) {
			if ($detail->isRuCard()) {
				array_push($rules['sum'], 'min:' . config('unitpay.withdrawal_restrictions.card_rf.min'));
				array_push($rules['sum'], 'max:' . config('unitpay.withdrawal_restrictions.card_rf.max'));
			} else {
				array_push($rules['sum'], 'min:' . config('unitpay.withdrawal_restrictions.card_not_rf.min'));
				array_push($rules['sum'], 'max:' . config('unitpay.withdrawal_restrictions.card_not_rf.max'));
			}
		} elseif ($detail->isWebmoney()) {
			array_push($rules['sum'], 'min:' . config('unitpay.withdrawal_restrictions.webmoney.min'));
			array_push($rules['sum'], 'max:' . config('unitpay.withdrawal_restrictions.webmoney.max'));
		} elseif ($detail->isQiwi()) {
			array_push($rules['sum'], 'min:' . config('unitpay.withdrawal_restrictions.qiwi.min'));
			array_push($rules['sum'], 'max:' . config('unitpay.withdrawal_restrictions.qiwi.max'));
		} elseif ($detail->isYandex()) {
			array_push($rules['sum'], 'min:' . config('unitpay.withdrawal_restrictions.yandex.min'));
			array_push($rules['sum'], 'max:' . config('unitpay.withdrawal_restrictions.yandex.max'));
		}

		return $rules;
	}

	public function attributes()
	{
		return __('user_outgoing_payment');
	}
}
