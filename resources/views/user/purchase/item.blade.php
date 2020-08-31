<tr>
	<td>{{ $purchase->id }}</td>
	<td>
		<x-user-name :user="$purchase->buyer"/>
	</td>
	<td>
		<x-user-name :user="$purchase->seller"/>
	</td>
	<td>
		@if ($purchase->isBook())
			<x-book-name :book="$purchase->purchasable"/>
		@endif
	</td>
	<td>
		<x-time :time="$purchase->created_at"/>
	</td>
	<td>
		@if ($purchase->isCanceled())
			<x-time :time="$purchase->canceled_at"/>
		@endif
	</td>
	<td>
		<div class="dropdown">
			<button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton_{{ $purchase->id }}" data-toggle="dropdown"
					aria-haspopup="true"
					aria-expanded="false">
				<i class="fas fa-ellipsis-v"></i>
			</button>
			<div class="dropdown-menu" aria-labelledby="dropdownMenuButton_{{ $purchase->id }}">
				<a class="dropdown-item" href="{{ route('purchases.cancel', $purchase) }}">{{ __('user_purchases.cancel_the_purchase') }}</a>
			</div>
		</div>
	</td>
</tr>