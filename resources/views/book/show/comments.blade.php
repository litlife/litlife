@if (isset($comments) and count($comments) > 0)

	@if ($comments->hasPages())
		<div class="row mt-3">
			<div class="col-12">
				{{ $comments->appends(request()->except(['page', 'ajax']))->links() }}
			</div>
		</div>
	@endif

	@php ($rand = rand(3, 6))


	@foreach ($comments as $comment)

		@isset($book)
			@if (empty($alertCommentsFromOtherPublicationsShown) and $comment->origin_commentable_id != $book->id)
				@php($alertCommentsFromOtherPublicationsShown = true)
				<div class="alert alert-info mt-3" role="alert">
					{{ __('book.below_are_comments_from_other_versions_and_publications') }}
				</div>
			@endif
		@endisset

		@include("comment.list.default", ['book' => $book, 'item' => $comment])

		@if ($loop->index == $rand)
			@can('see_ads', \App\User::class)
				<div class="col-12 p-2">
					@include('ads.adaptive_horizontal')
				</div>
			@endcan
		@endif
	@endforeach


	@if ($comments->hasPages())
		<div class="row">
			<div class="col-12">
				{{ $comments->appends(request()->except(['page', 'ajax']))->links() }}
			</div>
		</div>
	@endif

@else
	@if (empty($top_comments) or count($top_comments) < 1)
		<div class="row">
			<div class="col-12">
				<div class="alert alert-info">
					{{ __('comment.nothing_found') }}
				</div>
			</div>
		</div>
	@endif
@endif