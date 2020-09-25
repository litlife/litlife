@if (!empty($descendants))
	@foreach ($descendants as $descendant)
		@if (preg_match('/\,'.$item->id.'\,$/i', $descendant->tree))
			@include('forum.post.item.default', ['item' => $descendant, 'parent' => $item])
		@endif
	@endforeach
@endif