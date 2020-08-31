@if ($blogs->hasPages())
	<div class="row mt-3">
		<div class="col-12">
			{{ $blogs->appends(request()->except(['page', 'ajax']))->links() }}
		</div>
	</div>
@endif

<div data-user-id="{{ $user->id }}">
	@if(count($blogs) > 0)
		@foreach ($blogs as $blog)
			@include('blog.list.default', ['item' => $blog])
		@endforeach
	@else
		<div class="alert alert-info" role="alert">
			{{ __('blog.nothing_found') }}
		</div>
	@endif
</div>

@if ($blogs->hasPages())
	<div class="row mt-3">
		<div class="col-12">
			{{ $blogs->appends(request()->except(['page', 'ajax']))->links() }}
		</div>
	</div>
@endif

