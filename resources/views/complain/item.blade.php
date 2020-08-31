<div class="complain card mb-3" data-id="{{ $item->id }}" data-type="{{ $item->getComplainableName() }}">
	<div class="card-header">
		{{ __('complain.complained_by_user') }}
		<x-user-name :user="$item->create_user"/>
		<x-time :time="$item->created_at"/>
	</div>
	<div class="card-body">

		<p>{{ $item->text }}</p>

		<div class="status">
			@include('complain.status')
		</div>

		<div class="complain_buttons mb-3">
			<a class="btn-start-review btn btn-outline-success" href="{{ route('complains.start_review', $item) }}"
			   @cannot ('startReview', $item) style="display:none;" @endcannot>
				{{ __('complain.start_review') }}
			</a>

			<a class="btn-approve btn btn-outline-success" href="{{ route('complains.approve', $item) }}"
			   @cannot ('approve', $item) style="display:none;" @endcannot>
				<i class="far fa-check-circle"></i> {{ __('complain.mark_as_reviewed') }}
			</a>

			<a class="btn-stop-review btn btn-outline-secondary"
			   href="{{ route('complains.stop_review', $item) }}"
			   @cannot ('stopReview', $item) style="display:none;" @endcannot>
				<i class="far fa-times-circle"></i> {{ __('complain.stop_review_request') }}
			</a>
		</div>

		<div class="complainable">
			@if ($item->complainable instanceof \App\Comment)
				@include('comment.list.default', ['item' => $item->complainable])
			@elseif ($item->complainable instanceof \App\Post)
				@include('forum.post.item.default', ['item' => $item->complainable])
			@elseif ($item->complainable instanceof \App\Blog)
				@include('blog.list.default', ['item' => $item->complainable])
			@elseif ($item->complainable instanceof \App\Book)
				@include('complain.complainable.book', ['book' => $item->complainable])
			@endif
		</div>
	</div>
</div>