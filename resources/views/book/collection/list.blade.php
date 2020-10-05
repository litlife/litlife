@if ($collections->count())
	<div class="list-group mb-3">
		@foreach ($collections as $collection)
			@include('book.collection.item', ['item' => $collection, 'book' => $book])
		@endforeach
	</div>
@else
	<div class="alert alert-info">
		{{ __('None of the collection is not found') }}
	</div>
@endif

@if ($collections->hasPages())
	{{ $collections->appends(request()->except(['page', 'ajax']))->links() }}
@endif