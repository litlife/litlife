<tr class="forum" data-id="{{ $forum->id }}">
	<td>
		<h4 class="title h6">
			<a href="{{ route('forums.show', compact('forum')) }}">
				{{ $forum->name }}
			</a>
		</h4>

		<small class="text-muted d-block">
			{{ $forum->description }}

			@if ($forum->users_with_access->count())
				<div class="members">
					{{ __('forum.members_count') }}: {{ $forum->users_with_access->count()  }}
					<a href="javascript:void(0)" class="show_all ">{{ __('forum.members_show_all') }}</a>
					<span class="list" style="display: none;">
						@foreach($forum->users_with_access as $user)
							<x-user-name :user="$user"/>{{ $loop->last ? '' : ', ' }}
						@endforeach
                    </span>
				</div>
			@endif

		</small>
	</td>
	<td class="text-center">
		{{ $forum->topic_count }}
	</td>
	<td class="text-center">
		{{ $forum->post_count }}
	</td>
	<td>
		<x-user-avatar :user="optional($forum->last_post)->create_user" width="32" height="32" href="1" class="lazyload rounded pull-left"/>
	</td>
	<td>
		@if (!empty($forum->last_post))
			@if (!empty($forum->last_post->create_user))
				{{ __('common.from') }}
				<x-user-name :user="optional($forum->last_post)->create_user"/> <br/>
			@endif

			<x-time :time="optional($forum->last_post)->created_at"/><br/>

			@if (!empty($forum->last_topic))
				{{ __('forum.in_topic') }}
				<a href="{{ route('posts.go_to', ['post' => $forum->last_post]) }}">
					{{ $forum->last_topic->name }}
				</a>
			@endif
		@endif
	</td>
	<td>

		<div class="btn-group">
			<button class="btn btn-light dropdown-toggle" type="button" id="dropdownMenuButton"
					data-toggle="dropdown"
					aria-haspopup="true"
					aria-expanded="false">
				<i class="fas fa-ellipsis-h"></i>
			</button>
			<div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">

				@can ('change_order', $forum)
					<a href="javascript:void(0)" class="move_forum pointer dropdown-item">
						<i class="fas fa-arrows-alt-v"></i>
						{{ __('common.move') }}
					</a>
				@endcan

				@can ('update', $forum)
					<a class="dropdown-item"
					   href="{{ route('forums.edit', compact('forum')) }}">
						{{ __('common.edit') }}
					</a>
				@endcan

				<a class="delete pointer dropdown-item" disabled="disabled"
				   data-loading-text="{{ __('common.deleting') }}..."
				   @cannot ('delete', $forum) style="display:none;"@endcannot>
					{{ __('common.delete') }}
				</a>

				<a class="restore pointer dropdown-item" disabled="disabled"
				   data-loading-text="{{ __('common.restoring') }}"
				   @cannot ('restore', $forum) style="display:none;"@endcannot>
					{{ __('common.restore') }}
				</a>

			</div>
		</div>
	</td>
</tr>