@extends('layouts.app')

@section('content')

	@if (session('success'))
		<div class="alert alert-success">
			{{ session('success') }}
		</div>
	@endif

	<div class="card mb-2">
		<div class="card-body">
			{{ __('user_payment_transaction.allowed_balance') }}: {{ $user->balance() }} р.

			@if ($user->frozen_balance() > 0)
				{{ __('user_payment_transaction.frozen_balance') }}: {{ $user->frozen_balance() }} р.
			@endif

			<hr/>

			<div class="btn-margin-bottom-1">

				<a href="{{ route('users.wallet.deposit', $user) }}" class="btn btn-primary">
					<i class="fas fa-sign-in-alt"></i> {{ __('user_payment_transaction.deposit_balance') }}
				</a>

				@can('withdrawal', $user)
					<a href="{{ route('users.wallet.withdrawal', $user) }}" class="btn btn-primary">
						{{ __('user_payment_transaction.withdrawal_from_balance') }} <i class="fas fa-sign-out-alt"></i>
					</a>

					@if ($user->wallets->count() < 1)
						<a href="{{ route('users.wallet.withdrawal', $user) }}" class="btn btn-primary">
							{{ __('user_outgoing_payment.add_wallet') }}
						</a>
					@endif

				@endcan

				@can('transfer_money', $user)
					<a href="{{ route('users.wallet.transfer', ['user' => $user]) }}" class="btn btn-primary">
						{{ __('user_payment_transaction.transfer_to_other_user') }}
					</a>
				@endcan

			</div>
		</div>
	</div>


	<div class="card">
		<div class="card-header">
			{{ __('user_payment_transaction.history') }}
		</div>
		<div class="card-body">
			@if ($transactions->count() < 1)
				<div class="text-center px-2">
					{{ __('user_payment_transaction.no_transactions_found') }}
				</div>
			@else
				<div class="table-responsive">
					<table class="table table-sm">
						<thead>
						<tr>
							<th scope="col" class="text-center">#</th>
							<th scope="col" class="text-center">{{ __('user_payment_transaction.type') }}</th>
							<th scope="col">{{ __('user_payment_transaction.description') }}</th>
							<th scope="col" class="text-center">{{ __('user_payment_transaction.status') }}</th>
							<th scope="col" class="text-center">{{ __('common.date') }}</th>
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