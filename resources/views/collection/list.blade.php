@if (!empty($collections) and $collections->count())
	<div class="collections">
		@foreach ($collections as $item)
			@include('collection.item')
		@endforeach
	</div>
@else
	<div class="alert alert-info">{{ __('collection.nothing_found') }}</div>
@endif

<div class="row mt-3">
	<div class="col-12 ">
		@if (isset($collections) and $collections->hasPages())
			{{ $collections->appends(request()->except(['page', 'ajax']))->links() }}
		@endif
	</div>
</div>