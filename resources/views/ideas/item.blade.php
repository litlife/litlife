<div class="idea list-group-item list-group-item-action flex-column-reverse flex-md-row d-flex" data-id="{{ $item->id }}">

	<div class="flex-shrink-0 mr-3 text-center flex-md-column flex-row col-md-4 col-lg-3 col-xl-2 d-flex align-content-center">
		@if (!empty($item->top_post))
			<div class="font-weight-bold"><h5 class="mb-0 counter">{{ $item->top_post->like_count }}</h5></div>

			<div class="ml-2">{{ trans_choice('idea.votes', $item->top_post->like_count) }}</div>

			<div class="you_support_this_idea ml-2" style="@if (!$item->top_post->authUserLike) display:none; @endif">
				<i class="fas fa-heart" style="color:red"></i> {{ __('idea.you_supported_this_idea') }}
			</div>

			@if (in_array($item->label, [\App\Enums\TopicLabelEnum::IdeaOnReview, \App\Enums\TopicLabelEnum::IdeaInProgress]))
				<a href="{{ route('likes.store', ['type' => 'post', 'id' => $item->top_post->id]) }}"
				   style="@if ($item->top_post->authUserLike) display:none; @endif"
				   class="like ml-2 btn btn-primary btn-sm text-nowrap">
					<i class="far fa-heart"></i> {{ __('idea.support') }}
				</a>
			@endif
		@endif
	</div>

	<div class="w-100">

		<div class="d-flex w-100 justify-content-between">
			<h5 class="mb-1">
				<a href="{{ route('topics.show', $item) }}">{{ $item->name }}</a>
			</h5>
		</div>
		@if ($item->top_post)
			<p class="mb-1">{!! mb_substr(strip_tags($item->top_post->html_text), 0, 200) !!}
				<a class="text-info" href="{{ route('topics.show', $item) }}">{{ __('common.more') }}</a>
			</p>
		@endif

		<div>
			@switch ($item->label)
				@case (\App\Enums\TopicLabelEnum::IdeaImplemented)
				<span class="badge badge-success">{{ __('topic.labels.IdeaImplemented') }}</span>
				@break
				@case (\App\Enums\TopicLabelEnum::IdeaOnReview)

				@break
				@case (\App\Enums\TopicLabelEnum::IdeaInProgress)
				<span class="badge badge-info">{{ __('topic.labels.IdeaInProgress') }}</span>
				@break
				@case (\App\Enums\TopicLabelEnum::IdeaRejected)
				<span class="badge badge-danger">{{ __('topic.labels.IdeaRejected') }}</span>
				@break
			@endswitch
		</div>

	</div>

</div>