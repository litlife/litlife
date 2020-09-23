@if (!empty($descendants))
	@foreach ($descendants as $descendant)
		@if ($descendant->isDescendantOf($item))
			@include('forum.post.item.default', ['item' => $descendant, 'parent' => $item])
		@endif
	@endforeach
@endif