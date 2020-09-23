@if (!empty($descendants))
	@foreach ($descendants as $descendant)
		@if ($descendant->isDescendantOf($item))
			@include('blog.list.default', ['item' => $descendant, 'parent' => $item])
		@endif
	@endforeach
@endif