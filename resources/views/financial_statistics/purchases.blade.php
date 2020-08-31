@extends('layouts.app')

@section('content')

	<div class="card">

		<div class="card-body">

			@if ($purchases->count() < 1)
				<div class="alert alert-info">
					{{ __('user_purchases.no_sales_were_found') }}
				</div>
			@else

				<table class="table table-sm">
					<thead>
					<tr>
						<th scope="col">#</th>
						<th scope="col">{{ __('user_purchases.buyer') }}</th>
						<th scope="col">{{ __('user_purchases.seller') }}</th>
						<th scope="col">{{ __('user_purchases.purchasable') }}</th>
						<th scope="col">{{ __('user_purchases.created_at') }}</th>
						<th scope="col">{{ __('user_purchases.canceled_at') }}</th>
						<th scope="col"></th>
					</tr>
					</thead>
					<tbody>
					@foreach ($purchases as $purchase)
						@include('user.purchase.item')
					@endforeach
					</tbody>
				</table>
				@if ($purchases->hasPages())
					{{ $purchases->appends(request()->except(['page', 'ajax']))->links() }}
				@endif

			@endif

		</div>
	</div>

@endsection