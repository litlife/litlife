@if(count($comments) > 0)

	@php ($rand = rand(3, 6))

	@foreach ($comments as $comment)
		@include($view_name, [
			'item' => $comment,
			'no_child_toggle' => true,
			'no_limit' => true,
			'go_to_button' => true,
			'no_user_comment_counts' => true
		])

		@if ($loop->index == $rand)
			@can('see_ads', \App\User::class)
				<div class="col-12 p-2">
					<x-ad-block name="adaptive_horizontal"/>
				</div>
			@endcan
		@endif

	@endforeach

	@if ($comments->hasPages())

		{{ $comments->appends(request()->except(['page', 'ajax', 'with_panel']))->links() }}

	@endif

@else

	<div class="alert alert-info">{{ __('comment.nothing_found') }}</div>

@endif
