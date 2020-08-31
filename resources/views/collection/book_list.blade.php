@if(empty($books) or !$books->count())
	<div class="alert alert-info">
		{{ __('book.nothing_found') }}
	</div>
@else

	<div class="list-group books mb-3" style="height: 300px; overflow-y: auto">
		@foreach ($books as $book)
			@include('collection.book_list_item')
		@endforeach
	</div>

	<div class="">
		@if ($books->hasPages())
			{{ $books->appends(request()->except(['page']))->links('vendor.pagination.simple-bootstrap-4_without_current_page_and_per_page') }}
		@endif
	</div>

@endif

