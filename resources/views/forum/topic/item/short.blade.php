@if (!empty($item->last_post))

	<a href="{{ route('posts.go_to', ['post' => $item->last_post]) }}"
	   class="d-flex list-group-item item list-group-item-action" data-id="{{ $item->id }}"
	   data-forum-id="{{ empty($item->forum) ? : $item->forum->id }}">

		<div style="width:calc(100% - 60px);">
			<div class="mb-2 d-flex align-items-center flex-wrap">
				<div class="d-inline-block mr-2 text-truncate">
					{{ $item->name }}
				</div>

				@if ($item->forum->isIdeaForum())

					<div class="d-inline-block">
						@switch ($item->label)
							@case (\App\Enums\TopicLabelEnum::IdeaImplemented)
							<span class="badge badge-success">{{ __('topic.labels.IdeaImplemented') }}</span>
							@break
							@case (\App\Enums\TopicLabelEnum::IdeaOnReview)
							<span class="badge badge-info">{{ __('topic.labels.IdeaOnReview') }}</span>
							@break
							@case (\App\Enums\TopicLabelEnum::IdeaInProgress)
							<span class="badge badge-info">{{ __('topic.labels.IdeaInProgress') }}</span>
							@break
							@case (\App\Enums\TopicLabelEnum::IdeaRejected)
							<span class="badge badge-danger">{{ __('topic.labels.IdeaRejected') }}</span>
							@break
						@endswitch
					</div>
				@endif
			</div>

			<div class="d-flex align-items-center flex-wrap small">
				<div class="">
					<x-user-avatar :user="$item->last_post->create_user" width="20" height="20" href="0" class="lazyload rounded pull-left"/>
				</div>
				@if (!empty($item->last_post))
					<div class="ml-2 mr-2">
						<x-user-name :user="$item->last_post->create_user" href="0"/>
					</div>
					<div class="">
						<x-time :time="$item->last_post->created_at"/>
					</div>
				@endif
			</div>
		</div>
		<div class="d-flex justify-content-center align-items-center pl-3" style="width:60px;">
			<div class="text-decoration-none d-flex flex-column flex-md-row justify-content-center align-items-center">
				<i class="far fa-comment d-inline-block"></i>
				<div class="d-inline-block text-center ml-md-1">{{ $item->post_count }}</div>
			</div>
		</div>
	</a>

@endif