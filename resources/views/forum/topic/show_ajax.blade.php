@if ($items->hasPages())

	{{ $items->appends(request()->except(['page', 'ajax']))->links() }}

@endif

@php ($rand = rand(3, 5))

@if(count($items) > 0)
	@foreach ($items as $item)
		@include ('forum.post.item.default', [
				'item' => $item,
				'no_topic_forum_links' => true,
				'no_go_to_forum_button' => true,
				'achievements' => true
				])

		@if ($loop->index == $rand)
			@can('see_ads', \App\User::class)
				<div class="col-12 p-2">
					@include('ads.adaptive_horizontal')
				</div>
			@endcan
		@endif

		@if ($loop->index == $rand + 3)
			@can('see_ads', \App\User::class)
				<div class="col-12 p-2">
					@include('ads.adaptive_horizontal_second')
				</div>
			@endcan
		@endif

	@endforeach
@else
	@if (empty($topic->top_post))
		<div class="alert alert-info" role="alert">
			{{ __('post.nothing_found') }}
		</div>
	@endif
@endif

@if ($items->hasPages())
	{{ $items->appends(request()->except(['page', 'ajax']))->links() }}
@endif