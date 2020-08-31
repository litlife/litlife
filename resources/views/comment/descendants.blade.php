@if (!empty($descendants))
	@foreach ($descendants as $descendant)
		@if (preg_match('/\,'.$comment->id.'\,$/i', $descendant->tree))
			@include('comment.list.default', ['item' => $descendant, 'parent' => $comment, 'no_book_link' => true])
		@endif
	@endforeach
@endif