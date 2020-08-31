@can ('buy_button', $book)
	<div>
		<span class="font-weight-bold small">{{ __('book.price') }}:</span>
		<span class="text-body font-weight-bold">{{ $book->price }} р.</span>
		@if ($book->isPriceHasBecomeLess())
			<s class="text-secondary">{{ $book->previous_price }} р.</s>
			<span class="badge font-weight-bold badge-danger">-{{ $book->getDiscount() }}%</span>
		@endif
	</div>
@endcan