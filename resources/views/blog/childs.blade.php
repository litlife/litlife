@if (!empty($descendants))
	@foreach ($descendants as $descendant)
		@if ($descendant->isChildOf($item))
			@include('blog.list.default', ['item' => $descendant, 'parent' => $item])
		@endif
	@endforeach
@endif