@if (!empty($blogs) and $blogs->count())
	<div class="blogs">
		@foreach ($blogs as $item)
			@include('blog.item')
		@endforeach
	</div>
@else
	<div class="alert alert-info">{{ __('blog.nothing_found') }}</div>
@endif

<div class="row mt-3">
	<div class="col-12 ">
		@if (isset($blogs) and $blogs->hasPages())
			{{ $blogs->appends(request()->except(['page', 'ajax']))->links() }}
		@endif
	</div>
</div>