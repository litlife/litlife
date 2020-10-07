<div class="list-group-item book" data-book-id="{{ $book->id }}">
	<div class="d-flex">
		<div class="mr-3" style="width: 90px;">
			<x-book-cover :book="$book" width="80" height="80" style="max-width: 100%;"/>
		</div>
		<div class="w-100">
			<div class="d-flex w-100 justify-content-between">
				<h6 class="mb-1">
					<x-book-name :book="$book"/>
				</h6>
			</div>
			<p class="mb-1">
				@foreach ($book->writers as $author)
					<x-author-name :author="$author" showOnline="0"/>{{ $loop->last ? '' : ', ' }}
				@endforeach
			</p>
			<p class="mb-1">
				@if ($book->collections->first())
					{{ __('In collection') }}
				@else
					<button class="select btn btn-primary">{{ __('common.select') }}</button>
				@endif
			</p>
		</div>
	</div>
</div>