<div class="mb-3">
	@foreach ($items as $item)
		@include('ideas.item', ['item' => $item])
	@endforeach
</div>

@if ($items->hasPages())
	{{ $items->appends(request()->except(['page', 'ajax']))->links() }}
@endif