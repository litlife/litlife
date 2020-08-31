<tr class="item" data-id="{{ $item->id }}" data-forum-id="{{ empty($item->forum) ? : $item->forum->id }}">
	<td>
		<h3 class="name h6">
			@if (!empty($main_priority_show) and !empty($item->main_priority))
				<i class="fas fa-thumbtack"></i>
			@endif

			@if (!empty($forum_priority_show) and !empty($item->forum_priority))
				<i class="fas fa-thumbtack"></i>
			@endif

			<a href="{{ route('topics.show', ['topic' => $item]) }}">{{ $item->name }}</a>

			@if ($item->isClosed())
				<i class="fas fa-ban"></i>
			@endif

			@if (!empty($item->forum) and $item->forum->isOrderTopicsBasedOnFixPostLikes() and !empty($item->like_count))
				<span class="text-nowrap"><i class="fas fa-heart" style="color:red"></i>&nbsp;{{ $item->like_count }}</span>
			@endif
		</h3>

		<small class="description text-muted">

			{{ $item->description }}

			@can ('see_technical_information', \App\User::class)

				@if (!empty($main_priority_show) and !empty($item->main_priority))
					<br/> {{ __('topic.main_priority') }}: {{ $item->main_priority }}
				@endif

				@if (!empty($forum_priority_show) and !empty($item->forum_priority))
					<br/> {{ __('topic.forum_priority') }}: {{ $item->forum_priority }}
				@endif
			@endcan
		</small>

		@if ($item->forum->isIdeaForum())

			<div>
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


	</td>

	<td class="text-center">
		{{ $item->post_count }}
	</td>

	<td>
		<x-user-avatar :user="optional($item->last_post)->create_user" width="32" height="32" href="1" class="lazyload rounded pull-left"/>
	</td>
	<td>
		@if (!empty($item->last_post))
			от
			<x-user-name :user="optional($item->last_post)->create_user"/>
			<x-time :time="optional($item->last_post)->created_at"/>
			<br/>
			<a class="break-word-disable" href="{{ route('posts.go_to', $item->last_post) }}">
				{{ __('forum.to_message') }} <i class="far fa-arrow-alt-circle-right"></i>
			</a>

		@endif
	</td>

	<td>

		<div class="btn-group" data-toggle="tooltip" data-placement="top" title="{{ __('common.open_actions') }}">
			<button class="btn btn-light dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown"
					aria-haspopup="true"
					aria-expanded="false">
				<i class="fas fa-ellipsis-h"></i>
			</button>
			<div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">

				@can ('update', $item)


					@can ('edit_spectial_settings', $item)
						@if ($item->forum->isIdeaForum())
							<a class="dropdown-item text-lowercase"
							   href="{{ route('topics.label.change', ['topic' => $item, 'label' => \App\Enums\TopicLabelEnum::IdeaInProgress]) }}">
								{{ __('topic.change_label_to') }} "{{ __('topic.labels.IdeaInProgress') }}"
							</a>

							<a class="dropdown-item text-lowercase"
							   href="{{ route('topics.label.change', ['topic' => $item, 'label' => \App\Enums\TopicLabelEnum::IdeaRejected]) }}">
								{{ __('topic.change_label_to') }} "{{ __('topic.labels.IdeaRejected') }}"
							</a>

							<a class="dropdown-item text-lowercase"
							   href="{{ route('topics.label.change', ['topic' => $item, 'label' => \App\Enums\TopicLabelEnum::IdeaImplemented]) }}">
								{{ __('topic.change_label_to') }} "{{ __('topic.labels.IdeaImplemented') }}"
							</a>

							<a class="dropdown-item text-lowercase"
							   href="{{ route('topics.label.change', ['topic' => $item, 'label' => \App\Enums\TopicLabelEnum::IdeaOnReview]) }}">
								{{ __('topic.change_label_to') }} "{{ __('topic.labels.IdeaOnReview') }}"
							</a>

							<div class="dropdown-divider"></div>
						@endif
					@endcan

					<a class="dropdown-item text-lowercase" href="{{ route('topics.edit', ['topic' => $item]) }}">
						{{ __('common.edit') }}
					</a>
				@endcan

				<a class="delete pointer dropdown-item text-lowercase" disabled="disabled"
				   data-loading-text="{{ __('common.deleting') }}..."
				   @cannot ('delete', $item) style="display:none;"@endcannot>
					{{ __('common.delete') }}
				</a>

				<a class="restore pointer dropdown-item text-lowercase" disabled="disabled"
				   data-loading-text="{{ __('common.restoring') }}"
				   @cannot ('restore', $item) style="display:none;"@endcannot>
					{{ __('common.restore') }}
				</a>

				@can ('merge', $item)
					<a class="dropdown-item text-lowercase"
					   href="{{ route('topics.merge_form', ['topic' => $item]) }}">
						{{ __('common.merge') }}
					</a>
				@endcan

				@if ($item->isClosed())
					@can ('open', $item)
						<a class="dropdown-item text-lowercase" href="{{ route('topics.open', ['topic' => $item]) }}">
							{{ __('common.open') }}
						</a>
					@endcan
				@else
					@can ('close', $item)
						<a class="dropdown-item text-lowercase" href="{{ route('topics.close', ['topic' => $item]) }}">
							{{ __('common.close') }}
						</a>
					@endcan
				@endif

				@can ('move', $item)
					<a class="dropdown-item text-lowercase"
					   href="{{ route('topics.move_form', ['topic' => $item]) }}">
						{{ __('common.move') }}
					</a>
				@endcan

				@if (!$item->isArchived())
					@can ('archive', $item)
						<a class="dropdown-item text-lowercase" href="{{ route('topics.archive', ['topic' => $item]) }}">
							{{ __('common.archive') }}
						</a>
					@endcan
				@else
					@can ('unarchive', $item)
						<a class="dropdown-item text-lowercase" href="{{ route('topics.unarchive', ['topic' => $item]) }}">
							{{ __('common.unarchive') }}
						</a>
					@endcan
				@endif

			</div>
		</div>

	</td>
</tr>