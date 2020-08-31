@extends('layouts.app')

@section('content')

	<div class="card mb-3">
		<div class="card-body">

			{{ __('financial_statistic.today_profit') }}: <strong>{{ $user->today_profit() }}</strong> р. <br/>

			{{ __('financial_statistic.this_month_profit') }}: <strong>{{ $user->month_profit() }}</strong> р. <br/>

			{{ __('financial_statistic.site_balance') }}: <strong>{{ $user->balance(true) }}</strong> р. <br/>

			@if (!is_null($request))
				{{ __('financial_statistic.unitpay_balance') }}:
				@if ($request->isSuccess())
					<strong>{{ $request->result()->balance }}</strong> р.
				@elseif ($request->isError())
					{{ __('common.error') }} {{ $request->getErrorCode() }}: {{ $request->getErrorMessage() }}
				@endif
				<br/>
			@endif

			{{ __('financial_statistic.users_sum_balances') }}: <strong>{{ $users_sum_balances }}</strong> р. <br/>

			{{ __('financial_statistic.all_waited_withdrawal_sum') }}: <strong>{{ $all_waited_withdrawal_sum }}</strong> р.
			<br/>

		</div>
	</div>

	<div class="card">
		@include('financial_statistics.card_header')
		<div class="card-body">

			@if ($transactions->count() < 1)
				<div class="alert alert-info">
					{{ __('user_payment_transaction.nothing_found') }}
				</div>
			@else
				<div class="table-responsive">
					<table class="table table-sm">
						<thead>
						<tr>
							<th scope="col">#</th>
							<th scope="col">{{ __('user_payment_transaction.type') }}</th>
							<th scope="col">{{ __('user_payment_transaction.description') }}</th>
							<th scope="col">{{ __('user_payment_transaction.status') }}</th>
							<th scope="col">{{ __('user_payment_transaction.status_change_at') }}</th>
						</tr>
						</thead>
						<tbody>
						@foreach ($transactions as $transaction)
							@include('user.wallet.item')
						@endforeach
						</tbody>
					</table>
					@if ($transactions->hasPages())
						{{ $transactions->appends(request()->except(['page', 'ajax']))->links() }}
					@endif
				</div>
			@endif
		</div>
	</div>

@endsection