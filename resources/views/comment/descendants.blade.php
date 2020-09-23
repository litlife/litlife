@if (!empty($descendants))
	@foreach ($descendants as $descendant)
		@if ($descendant->isDescendantOf($comment))
			@include('comment.list.default', ['item' => $descendant, 'parent' => $comment, 'no_book_link' => true])
		@endif
	@endforeach
@endif