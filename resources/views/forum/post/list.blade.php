@if(count($posts) > 0)


	@foreach ($posts as $post)
		@include('forum.post.item.'.$item_render, ['item' => $post, 'no_limit' => true, 'no_child_toggle' => true])
	@endforeach

	@if ($posts->hasPages())

		{{ $posts->appends(request()->except(['page', 'ajax']))->links() }}

	@endif

@else
	<p class="alert alert-info">{{ __('post.nothing_found') }}</p>
@endif
