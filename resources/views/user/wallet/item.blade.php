<tr>
	<td>{{ $transaction->id }}</td>

	@if (!empty($user_column))
		<td>
			<x-user-name :user="$transaction->user"/>
		</td>
	@endif

	@if ($transaction->isDeposit())

		<td><i class="fas fa-sign-in-alt"></i></td>
		<td>
			<div>
				{{ __('user_payment_transaction.deposit.'.$transaction->status, ['payment_type' => __('user_payment_transaction.payment_types_for_array.'.$transaction->operable->getPaymentType()), 'purse' => $transaction->operable->getPurse(), 'sum' => $transaction->sum]) }}
			</div>
			@if ($transaction->isStatusError())
				<div>
					{{ __('user_payment_transaction.the_payment_system_returned_an_error') }}: "{{ $transaction->operable->getPaymentError() }}"
				</div>
			@endif
			<div>
				@can('pay', $transaction)
					<a href="{{ route('users.transaction.pay', ['user' => $transaction->user, 'transaction' => $transaction]) }}"
					   class="btn btn-outline-primary btn-sm">
						{{ __('user_payment_transaction.continue_pay') }}
					</a>
				@endcan

				@can('cancel', $transaction)
					<a href="{{ route('users.transaction.cancel', ['user' => $transaction->user, 'transaction' => $transaction]) }}"
					   class="btn btn-outline-primary btn-sm">
						{{ __('user_payment_transaction.cancel_incoming_payment') }}
					</a>
				@endif
			</div>
		</td>

	@elseif ($transaction->isWithdrawal())

		<td><i class="fas fa-sign-out-alt"></i></td>
		<td>
			<div>
				{{ __('user_payment_transaction.withdrawal.'.$transaction->status, [
				'payment_type' => __('user_payment_transaction.payment_types_for_array.'.$transaction->operable->payment_type),
				'purse' => $transaction->operable->purse,
				'sum' => abs($transaction->sum) - $transaction->operable->getPayoutComission(),
				'comission' => $transaction->operable->getPayoutComission()
				]) }}
			</div>

			@if ($transaction->isStatusError())
				<div>
					{{ __('user_payment_transaction.the_payment_system_returned_an_error') }}:
					@if ($transaction->operable->getErrorCode() == 1000)
						{{ strip_tags($transaction->operable->getPaymentError()) }}
					@else
						{{ __('user_outgoing_payment.errors.'.$transaction->operable->getErrorCode()) }}
					@endif
				</div>
			@endif

			@can('cancel', $transaction)
				<div>
					<a href="{{ route('users.transaction.cancel', ['user' => $transaction->user, 'transaction' => $transaction]) }}"
					   class="btn btn-outline-primary btn-sm">
						{{ __('user_payment_transaction.cancel_outgoing_payment') }}
					</a>
				</div>
			@endif
		</td>
	@elseif ($transaction->isBuy())
		<td><i class="fas fa-shopping-cart"></i></td>
		<td>
			@if (!empty($transaction->operable) and $transaction->operable->isBook())
				{{ __('user_payment_transaction.buy', ['title' => optional($transaction->operable->purchasable)->title, 'sum' => abs($transaction->sum)]) }}
			@endif
		</td>
	@elseif ($transaction->isSell())
		<td><i class="fas fa-shopping-cart"></i></td>
		<td>
			@if (!empty($transaction->operable) and $transaction->operable->isBook())
				{{ __('user_payment_transaction.sell', ['title' => optional($transaction->operable->purchasable)->title, 'sum' => $transaction->sum]) }}
			@endif
		</td>
	@elseif ($transaction->isComission())
		<td><i class="fas fa-shopping-cart"></i></td>
		<td>
			@if (!empty($transaction->operable) and $transaction->operable->isBook())
				{{ __('user_payment_transaction.comission', ['title' => optional($transaction->operable->purchasable)->title, 'sum' => $transaction->sum]) }}
			@endif
		</td>
	@elseif ($transaction->isComissionRefererBuyer())
		<td><i class="fas fa-shopping-cart"></i></td>
		<td>
			@if (!empty($transaction->operable) and $transaction->operable->isBook())
				{{ __('user_payment_transaction.comission_for_refer_buyer', ['user_name' => $transaction->operable->buyer->userName, 'sum' => $transaction->sum]) }}
			@endif
		</td>
	@elseif ($transaction->isComissionRefererSeller())
		<td><i class="fas fa-shopping-cart"></i></td>
		<td>
			@if (!empty($transaction->operable) and $transaction->operable->isBook())
				{{ __('user_payment_transaction.comission_for_refer_seller', ['user_name' => $transaction->operable->seller->userName, 'sum' => $transaction->sum]) }}
			@endif
		</td>
	@elseif ($transaction->isReceipt())
		<td><i class="fas fa-sign-in-alt"></i></td>
		<td>
			@if (!empty($transaction->operable))
				{{ __('user_payment_transaction.receipt', ['user_name' => $transaction->operable->sender->userName, 'sum' => abs($transaction->sum)]) }}
			@endif
		</td>
	@elseif ($transaction->isTransfer())
		<td><i class="fas fa-sign-out-alt"></i></td>
		<td>
			@if (!empty($transaction->operable))
				{{ __('user_payment_transaction.transfer', ['user_name' => $transaction->operable->recepient->userName, 'sum' => abs($transaction->sum)]) }}
			@endif
		</td>
	@endif

	<td class="text-center">
		@if ($transaction->isStatusError())
			<i class="fas fa-exclamation-circle"></i>
		@elseif ($transaction->isStatusSuccess())
			<i class="fas fa-check-circle"></i>
		@elseif ($transaction->isStatusWait())
			<i class="far fa-clock"></i>
		@elseif ($transaction->isStatusProcessing())
			<i class="far fa-clock"></i>
		@elseif ($transaction->isStatusCanceled())
			<i class="fas fa-ban"></i>
		@endif
	</td>

	<td>
		<x-time :time="$transaction->status_changed_at"/>
	</td>

</tr>