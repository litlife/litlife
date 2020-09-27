@if (!empty($descendants))
	@foreach ($descendants as $descendant)
		@if ($descendant->isChildOf($post))
			@include('forum.post.item.default', ['item' => $descendant, 'parent' => $post, 'no_topic_forum_links' => true, 'no_go_to_forum_button' => true])
		@endif
	@endforeach
@endif