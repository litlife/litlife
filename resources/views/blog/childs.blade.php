@if (!empty($descendants))
	@foreach ($descendants as $descendant)
		@if (preg_match('/\,'.$item->id.'\,$/i', $descendant->tree))
			@include('blog.list.default', ['item' => $descendant, 'parent' => $item])
		@endif
	@endforeach
@endif