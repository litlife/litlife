@if(count($items) > 0)
	@foreach ($items as $item)
		@include('blog.list.default', [
		'item' => $item,
		'user' => $item->owner,
		'go_to_button' => true
		])
	@endforeach
@else
	<div class="col-12">
		<div class="alert alert-info" role="alert">
			{{ __('blog.nothing_found') }}
		</div>
	</div>
@endif


@if ($items->hasPages())
	<div class="row mt-3">
		<div class="col-12">
			{{ $items->appends(request()->except(['page', 'ajax']))->links() }}
		</div>
	</div>
@endif