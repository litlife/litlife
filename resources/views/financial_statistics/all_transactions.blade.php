@extends('layouts.app')

@section('content')

	<div class="card">
		@include('financial_statistics.card_header')
		<div class="card-body">

			@if ($transactions->count() < 1)
				<div class="alert alert-info">
					{{ __('user_payment_transaction.nothing_found') }}
				</div>
			@else

				<table class="table table-sm">
					<thead>
					<tr>
						<th scope="col">#</th>
						<th scope="col">{{ __('user_payment_transaction.user_id') }}</th>
						<th scope="col">{{ __('user_payment_transaction.type') }}</th>
						<th scope="col">{{ __('user_payment_transaction.description') }}</th>
						<th scope="col">{{ __('user_payment_transaction.status') }}</th>
						<th scope="col">{{ __('user_payment_transaction.status_change_at') }}</th>
					</tr>
					</thead>
					<tbody>
					@foreach ($transactions as $transaction)
						@include('user.wallet.item', ['user_column' => true])
					@endforeach
					</tbody>
				</table>
				@if ($transactions->hasPages())
					{{ $transactions->appends(request()->except(['page', 'ajax']))->links() }}
				@endif

			@endif

		</div>
	</div>

@endsection