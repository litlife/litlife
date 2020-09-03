@php(!empty($level) ?: $level = 0)

@component('components.comment', get_defined_vars())

	@slot('anchor')
		<span id="item_{{ $item->id }}" class="anchor"></span>
	@endslot

	@slot('data_attributes')

		@if (isset($parent))
			data-parent-id="{{ $parent->id }}"
		@endif

		@if (!empty($item->topic))
			data-topic-id="{{ $item->topic->id }}"
		@endif

		itemscope
		itemtype="http://schema.org/Comment"
		data-level="{{ $level ?? 0 }}"

	@endslot

	@slot('avatar')
		<x-user-avatar :user="$item->create_user" width="50" height="50"/>
	@endslot

	<h6 class="mb-2">
		@if ($item->isFixed())
			<i class="fas fa-thumbtack"></i>
		@endif

		@if (empty($achievements))
			<x-user-name :user="$item->create_user"/>
		@else
			<x-user-name :user="$item->create_user"/>
		@endif

		@if (isset($parent))
			{{ trans_choice('post.answer', $item->create_user->gender ?? 'unknown') }}

			<x-user-name :user="$parent->create_user"/>
		@endif

		<x-time :time="$item->created_at"/>
	</h6>


	@if (!empty($item->create_user))

		<div class="user-info text-muted mb-2">

			<small>
				@if (!empty($item->create_user->forum_message_count))
					<a class="text-muted" href="{{ route('users.posts', $item->create_user) }}">
						{{ $item->create_user->forum_message_count }}
						{{ trans_choice('post.posts_count', $item->create_user->forum_message_count) }}</a> &nbsp;&nbsp;
				@endif

				@foreach ($item->create_user->getGroupStatus() as $name)
					{{ $name }}{{ $loop->last ? '' : ', ' }}
				@endforeach
				&nbsp;
				@if (!empty($item->create_user->latest_user_achievements))
					@foreach ($item->create_user->latest_user_achievements as $latest_user_achievement)
						@include('achievement.badge', ['user_achievement' => $latest_user_achievement])
					@endforeach

					@if ($item->create_user->latest_user_achievements->count() < $item->create_user->achievements_count)
						<a class="btn btn-sm btn-light text-muted"
						   href="{{ route('users.achievements', ['user' => $item->create_user ?: null]) }}"
						   data-toggle="tooltip" data-placement="top" title="{{ __('achievement.show_all') }}">
							{{ __('common.more') }} {{ ($item->create_user->achievements_count-$item->create_user->latest_user_achievements->count()) }}
						</a>
					@endif
				@endif
			</small>
		</div>
	@endif


	<div class="mb-2">
		<div class="html_box imgs-fluid @if ($item->trashed()) transparency @endif"
			 style="max-height: 600px; overflow-y:hidden;">

			@if ($item->isSentForReview() and !$item->isAuthUserCreator() and empty($show_text_even_if_on_review))
				{{ trans_choice('post.on_check', 1) }}
			@else
				{!! $item->html_text !!}
			@endif

		</div>

		@if ($item->isEdited())
			<div class="mt-2">
				<small class="text-muted">
					@if ($item->edit_user)
						{{ trans_choice('user.edited', $item->edit_user->gender) }}
						<x-user-name :user="$item->edit_user"/>
					@else
						{{ __('post.edited') }}
					@endif

					<x-time :time="$item->user_edited_at"/>
				</small>
			</div>
		@endif
	</div>

	@if (empty($no_button_panel))

		<div class="btn-margin-bottom-1 buttons-panel">

			@include('like.item', ['item' => $item, 'like' => pos($item->likes) ?: null, 'likeable_type' => 'post'])

			@if (@$item->topic->top_post_id != $item->id)

				@can('create_post', $item->topic)
					@can('reply', $item)
						<a href="{{ route('posts.create', ['topic' => $item->topic, 'parent' => $item]) }}"
						   data-toggle="tooltip" data-placement="top" title="{{ __('common.reply') }}"
						   class="btn btn-light btn-reply">
							<i class="far fa-comment"></i>
						</a>
					@endcan
				@endcan

			@endif

			@if (empty($no_child_toggle))

				<button class="btn btn-light close_descendants"
						data-toggle="tooltip" data-placement="top" title="{{ __('common.hide_replies') }}"
						@if ($item->children_count < 1 or !$item->isHaveDescendant($descendants)) style="display:none;" @endif>

					<i class="far fa-comments"></i>
					<span class="counter">{{ $item->children_count }}</span>
				</button>

				<button class="btn btn-light open_descendants"
						data-toggle="tooltip" data-placement="top" title="{{ __('common.show_replies') }}"
						@if ($item->children_count < 1 or $item->isHaveDescendant($descendants)) style="display:none;" @endif>
					<i class="fas fa-comments"></i>
					<span class="counter">{{ $item->children_count }}</span>
				</button>

			@endif

			@if (empty($no_go_to_forum_button))
				<a href="{{ route('posts.go_to', ['post' => $item]) }}"
				   data-toggle="tooltip" data-placement="top" title="{{ __('post.go_to') }}"
				   class="btn btn-light">
					<i class="fas fa-angle-right"></i>
				</a>
			@endif

			@if (!empty($parent))
				<a href="#item_{{ $parent->id }}" class="btn btn-light"
				   data-toggle="tooltip" data-placement="top" title="{{ __('common.go_to_parent') }}">
					<i class="fas fa-arrow-up"></i>
				</a>
			@endif

			<button class="btn btn-light btn-compress" style="display: none;"
					data-toggle="tooltip" data-placement="top" title="{{ __('common.compress') }}">
				<i class="fas fa-compress"></i>
			</button>

			<button class="btn btn-light btn-expand" style="display: none;"
					data-toggle="tooltip" data-placement="top" title="{{ __('common.expand') }}">
				<i class="fas fa-expand"></i>
			</button>

			<button class="btn btn-light share" data-toggle="tooltip"
					data-title="{{ e($item->getShareTitle()) }}" data-description="{{ e($item->getShareDescription()) }}"
					data-url="{{ route('posts.go_to', ['post' => $item]) }}"
					data-placement="top" title="{{ __('post.share') }}">
				<i class="far fa-share-square"></i>
			</button>

			<div class="btn-group dropdown" style="position: static" data-toggle="tooltip" data-placement="top"
				 title="{{ __('common.open_actions') }}">
				<button class="btn btn-light dropdown-toggle" type="button" id="dropdownMenuButton_{{ $item->id }}"
						data-toggle="dropdown"
						aria-haspopup="true"
						aria-expanded="false">
					<i class="fas fa-ellipsis-h"></i>
				</button>
				<div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton_{{ $item->id }}">

					<a class="delete pointer dropdown-item text-lowercase" disabled="disabled"
					   data-loading-text="{{ __('common.deleting') }}..."
					   @cannot ('delete', $item) style="display:none;"@endcannot>
						{{ __('common.delete') }}
					</a>

					<a class="restore pointer dropdown-item text-lowercase" disabled="disabled"
					   data-loading-text="{{ __('common.restoring') }}..."
					   @cannot ('restore', $item) style="display:none;"@endcannot>
						{{ __('common.restore') }}
					</a>

					@can('update', $item)
						<a class="btn-edit dropdown-item text-lowercase"
						   href="{{ route("posts.edit", ['post' => $item]) }}">
							{{ __('common.edit') }}
						</a>
					@endcan

					<a class="abuse dropdown-item text-lowercase" target="_blank"
					   href="{{ route("complains.report", ['type' => 'post', 'id' => $item->id]) }}">
						{{ __('common.complain') }}
					</a>

					@if ($item->isFixed())
						@can('unfix', $item)

							<a class="dropdown-item text-lowercase"
							   href="{{ route("posts.unfix", ['post' => $item]) }}">
								{{ __('common.unfix') }}
							</a>

						@endcan

					@else
						@can('fix', $item)
							<a class="dropdown-item text-lowercase"
							   href="{{ route("posts.fix", ['post' => $item]) }}">
								{{ __('common.fix') }}
							</a>
						@endcan
					@endif

					<a class="dropdown-item text-lowercase" target="_blank"
					   href="{{ route("likes.users", ['id' => $item->id, 'type' => 'post']) }}">
						{{ __('post.who_likes') }}
					</a>

					@can('approve', $item)
						<a href="javascript:void(0)" class="approve dropdown-item text-lowercase">
							{{ __('common.approve') }}
						</a>
					@endcan

					<a class="get_link pointer dropdown-item text-lowercase"
					   href="{{ route('posts.go_to', ['post' => $item]) }}"
					   data-href="{{ route('posts.go_to', ['post' => $item]) }}">
						{{ __('common.link_to_message') }}
					</a>

					@can('see_technical_information', $item)
						<a class="get_user_agent pointer dropdown-item text-lowercase" href="javascript:void(0)"
						   data-link="{{ route('user_agents.show', ['model' => 'post', 'id' => $item->id]) }}">
							{{ __('common.device_info') }}
						</a>
					@endcan

				</div>
			</div>

			@if (empty($no_select))
				@if ($item->level < 1)
					<button class="btn btn-light">
						<input class="select" type="checkbox">
					</button>
				@endif

			@endif

		</div>

	@endif

	@if (empty($no_topic_forum_links))
		<div class="mt-1">
			<small class="text-muted">
				@if (!empty($item->topic))
					@if (!empty($item->forum))
						<a href="{{ route('forums.show', ['forum' => $item->forum]) }}">{{ $item->forum->name }}</a> -
					@endif

					<a href="{{ route('topics.show', ['topic' => $item->topic]) }}">{{ $item->topic->name }}</a>
				@endif
			</small>
		</div>
	@endif

	@slot('descendants')

		@include('forum.post.descendants', ['level' => $level + 1])

	@endslot

@endcomponent



