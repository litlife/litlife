@php(!empty($level) ?: $level = 0)

@component('components.comment', get_defined_vars())

	@slot('anchor')
		<span id="comment_{{ $item->id }}" class="anchor"></span>
	@endslot

	@slot('data_attributes')

		@if (isset($parent))
			data-parent-id="{{ $parent->id }}"
		@endif

		data-commentable-id="{{ $item->origin_commentable_id }}"
		data-commentable-type="{{ $item->commentable_type }}"
		itemscope
		itemtype="http://schema.org/Comment"

	@endslot

	@slot('avatar')

		<x-user-avatar :user="$item->create_user" width="50" height="50" href="1"/>

	@endslot

	<h6 class="mb-2">
            <span itemprop="author" itemscope itemtype="http://schema.org/Person">
				<x-user-name :user="$item->create_user" itemprop="name"/>
            </span>

		@if (isset($parent))
			{{ trans_choice('post.answer', $item->create_user->gender ?? 'unknown') }}
			<x-user-name :user="$parent->create_user"/>
		@endif

		<x-time :time="$item->created_at"/>

		<meta itemprop="datePublished" content="{{ $item->created_at->format('Y-m-d') }}">

		@can ('view', $item)
			@if ($item->isPrivate())
				<i class="private_status fas fa-lock" data-toggle="tooltip" data-placement="top"
				   title="{{ __('comment.private_tooltip') }}"></i>
			@endif
		@endcan
	</h6>

	@if (!empty($item->create_user))

		<p class="user-info text-muted mb-2">

			<small>
				@if (!empty($item->create_user->comment_count))
					<a class="text-muted"
					   href="{{ route('users.books.comments', $item->create_user) }}">
						{{ $item->create_user->comment_count }}
						{{ trans_choice('comment.comments_count', $item->create_user->comment_count) }}</a> &nbsp;
				@endif

				@if ($item->isBookType() and ($author = $item->getCreateUserBookAuthor()))

					<a href="{{ route('authors.show', ['author' => $author]) }}"
					   class="badge badge-pill badge-secondary badge-author">
						{{ __('comment.create_user_book_author_type.'.$author->pivot->getTypeKey()) }}
					</a>
					&nbsp;
					<span class="groups">
                        @foreach ($item->create_user->getGroupStatus(__('author.manager_characters.author')) as $name)
							{{ $name }}{{ $loop->last ? '' : ', ' }}
						@endforeach
                            </span>
				@else
					<span class="groups">
                    @foreach ($item->create_user->getGroupStatus() as $name)
							{{ $name }}{{ $loop->last ? '' : ', ' }}
						@endforeach
                        </span> &nbsp;
				@endif

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
		</p>
	@endif

	@can ('view', $item)
		@if (empty($no_book_link))
			@if ($item->isBookType())
				@if(!empty($book) and !empty($item->originCommentable) and $book->id == $item->originCommentable->id)

				@else
					<h6 class="mb-2">
						@isset($item->originCommentable)
							<x-book-name :book="$item->originCommentable"/>

							@if (!empty($item->originCommentable->getAuthorsWithType('writers')))
								-
								@foreach ($item->originCommentable->getAuthorsWithType('writers') as $author)
									<x-author-name :author="$author"/>{{ $loop->last ? '' : ', ' }}
								@endforeach
							@endif
						@endisset
					</h6>
				@endif
			@elseif ($item->isCollectionType())
				<h6 class="mb-2">
					@include ('collection.name', ['item' => $item->originCommentable])
				</h6>
			@endif
		@endif

		@if ($item->isBookType())
			@if (!empty($item->userBookVote))
				@if ($vote = $item->userBookVote)
					<h6 class="mb-2">
						{{ __('common.vote') }}:
						<x-book-vote :vote="$vote"/>
					</h6>
				@endif
			@endif
		@endif
	@endcan

	<div class="mb-2">

		<div itemprop="text" class="html_box imgs-fluid @if ($item->trashed()) transparency @endif"
			 style="max-height: 600px; overflow-y:hidden;">
			@can('view', $item)
				@if ($item->isSentForReview() and !$item->isAuthUserCreator() and empty($show_text_even_if_on_review))
					{{ trans_choice('comment.on_check', 1) }}
				@else
					{!! $item->text !!}
				@endif
			@endcan
		</div>

	</div>

	@can ('view', $item)
		@if (empty($no_button_panel))

			<div class="btn-margin-bottom-1 buttons-panel">

				@include('comment.like', ['item' => $item, 'vote' => @$item->votes->first()])

				@if (!empty($item->originCommentable))
					<a href="{{ route('comments.create', ['commentable_type' => $item->commentable_type, 'commentable_id' => $item->origin_commentable_id,
					'parent' => $item]) }}"
					   data-toggle="tooltip" data-placement="top" title="{{ __('common.reply') }}"
					   class="btn btn-light btn-reply">
						<i class="far fa-comment"></i>
					</a>
				@endcan

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

				@if (!empty($go_to_button))

					@if (!empty($item->originCommentable))

						<a class="btn btn-light"
						   data-toggle="tooltip" data-placement="top" title="{{ __('comment.go_to_comment') }}"
						   href="{{ route('comments.go', $item) }}">

							<i class="fas fa-angle-right"></i>
						</a>

					@endif
				@endif

				@if (!empty($parent))
					<a href="#comment_{{ $parent->id }}" class="btn btn-light"
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
						data-url="{{ route('comments.go', ['comment' => $item]) }}"
						data-placement="top" title="{{ __('comment.share') }}">
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

						@can ('publish', $item)
							<a class="publish pointer dropdown-item text-lowercase"
							   href="{{ route('comments.publish', $item) }}">
								{{ __('comment.publish') }}
							</a>
						@endcan

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

						@can ('approve', $item)
							<a class="approve pointer dropdown-item text-lowercase"
							   href="{{ route('comments.approve', $item) }}">
								{{ __('common.approve') }}
							</a>
						@endcan

						@can ('update', $item)
							<a class="btn-edit dropdown-item text-lowercase"
							   href="{{ route("comments.edit", ['comment' => $item]) }}">
								{{ __('common.edit') }}
							</a>
						@endcan

						<a class="abuse dropdown-item text-lowercase"
						   href="{{ route("complains.report", ['type' => 'comment', 'id' => $item->id]) }}">
							{{ __('common.complain') }}
						</a>

						@can ('viewWhoLikesOrDislikes', $item)

							<a class="dropdown-item text-lowercase" target="_blank"
							   href="{{ route("users.comments.who_likes", ['comment' => $item]) }}">
								{{ __('comment.who_likes') }}
							</a>

						<!--
						<a class="dropdown-item text-lowercase" target="_blank"
						   href="{{ route("users.comments.who_dislikes", ['comment' => $item]) }}">
							{{ __('comment.who_dislikes') }}
								</a>
-->

						@endcan

						<a class="dropdown-item get_link pointer text-lowercase" itemprop="url"
						   href="{{ route('comments.go', ['comment' => $item]) }}"
						   data-href="{{ route('comments.go', ['comment' => $item]) }}">
							{{ __('common.link_to_message') }}
						</a>

						@can('see_technical_information', $item)
							<a class="get_user_agent pointer dropdown-item text-lowercase" href="javascript:void(0)"
							   data-link="{{ route('user_agents.show', ['model' => 'comment', 'id' => $item->id]) }}">
								{{ __('common.device_info') }}
							</a>
						@endcan

					</div>
				</div>
			</div>

		@endif
	@endcan
	@slot('descendants')

		@include('comment.descendants', ['comment' => $item, 'level' => $level + 1])

	@endslot

@endcomponent