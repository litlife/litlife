<div data-id="{{ $item->id }}"
	 @if (isset($parent)) data-parent-id="{{ $parent->id }}" @endif
	 data-commentable-id="{{ $comment->commentable_id }}" data-commentable-type="{{ $comment->commentable_type }}"
	 class="media item"
	 style="padding-left: {{ $item->level_with_limit * 50 }}px">

	<a id="comment_{{ $item->id }}" class="anchor"></a>

	<div class="pull-left" style="text-align: center">
		@if (isset($item->create_user->avatar))
			<img class="media-object rounded" src="{{ $item->create_user->avatar->fullUrlMaxSize(80, 80) }}"
				 style="max-width:{{ $item->create_user->avatar->maxWidth }}px; max-height:{{ $item->create_user->avatar->maxHeight }}px;"/>
		@elseif (isset($item->create_user))
			<img class="media-object rounded" src="{{ config('litlife.noimage') }}?w=80&h=80"
				 alt="...">
		@endif

	</div>

	<div class="media-body">
		<div class="media-heading">
			<h4 class="inline">
				<x-user-name :user="$item->create_user"/>
			</h4>
			<span class="inline">{{ __('') }}Комментариев {{ $item->create_user->comment_count }}</span>
		</div>

		<div class="row">
			<div class="col-12">
				<div class="html">{!! $item->text !!}</div>
			</div>
		</div>
		<div class="row">
			<div class="col-12">

				@can ('vote', $item)

					<button
							class="up btn btn-light @if (auth()->check() and (@$item->votes->first()->vote > 0)) active @endif">
						<span class="glyphicon glyphicon-thumbs-up"></span> <span
								class="counter">{{ $item->vote_up }}</span>
					</button>

					<button
							class="down btn btn-light @if (auth()->check() and (@$item->votes->first()->vote < 0)) active @endif">
						<span class="glyphicon glyphicon-thumbs-down"></span> <span
								class="counter">{{ $item->vote_down }}</span>
					</button>

				@endcan

				@if (!empty($go_to_button))

					<a class="btn btn-light" href="{{ action('CommentController@go_To', $item) }}">
						{{ __('') }}Перейти к комментарию
					</a>

				@endif


				@can ('commentOn', $item->commentable)

					<a href="{{ action('CommentController@create', ['book' => $item->commentable->id, 'parent' => $item->id]) }}"
					   class="btn btn-light">
						{{ __('') }}Ответить
					</a>

				@endcan

				@if ($item->children_count > 0)
					@if (!isset($descendants))
						<button class="btn btn-light child-toggle" data-toggle-status="hidden">
							{{ __('') }}Показать / скрыть ответы
							<span class="badge">{{ $item->children_count }}</span>
						</button>
					@else
						<button class="btn btn-light child-toggle" data-toggle-status="shown">
							{{ __('') }}Показать / скрыть ответы
							<span class="badge">{{ $item->children_count }}</span>
						</button>
					@endif
				@endif

				<div class="dropdown inline">
					<button class="btn btn-light dropdown-toggle" type="button" data-toggle="dropdown">
						<span class="caret"></span>
					</button>
					<ul class="dropdown-menu dropdown-menu-right">
						<li>
							<a class="delete pointer" disabled="disabled" data-loading-text="{{ __('common.deleting') }}..."
							   @cannot ('delete', $item) style="display:none;"@endcannot>
								{{ __('') }}удалить
							</a>
						</li>
						<li>
							<a class="restore pointer" disabled="disabled" data-loading-text="{{ __('common.restoring') }}"
							   @cannot ('restore', $item) style="display:none;"@endcannot>
								{{ __('') }}восстановить
							</a>
						</li>

						@can ('approve', $comment)
							<li>
								<a class="approve pointer" href="{{ action('CommentController@approve', $comment) }}">
									{{ __('') }}Одобрить комментарий
								</a>
							</li>
						@endcan

						@can ('update', $item)
							<li>
								<a href="{{ action("CommentController@edit", ['comment' => $item]) }}">
									{{ __('') }}редактировать
								</a>
							</li>
						@endcan


						<li><a href="{{ action("ComplainController@create_edit", ['type' => 'comment', 'id' => $item->id]) }}"
							   class="abuse">
								{{ __('') }}пожаловаться
							</a></li>

						@can ('viewWhoLikesOrDislikes', $item)

							<li>
								<a target="_blank"
								   href="{{ action("UserListController@usersWhoLikesComment", ['comment' => $item]) }}">
									{{ __('') }}показать кому понравился комментарий</a>
							</li>
							<li><a target="_blank"
								   href="{{ action("UserListController@usersWhoDislikesComment", ['comment' => $item]) }}">
									{{ __('') }}показать кому не понравился комментарий</a>
							</li>
						@endcan
					</ul>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-12">

				<x-time :time="$item->created_at"/>

				<span>ID: {{ $item->id }}</span>

				@if ($item->level < 1)
					@if ($item->commentable_type == 'book')
						@if ((empty($book)) or ($book->id != $item->commentable->id))

							{{ __('') }}Книга:&nbsp;<a
									href="{{ route('books.show', $item->commentable) }}">{{ $item->commentable->title }}</a>

							@if (!empty($item->commentable->writers))
								-

								@foreach ($item->commentable->writers as $author)
									<x-author-name :author="$author"/>{{ $loop->last ? '' : ', ' }}
								@endforeach

							@endif

						@endif
					@endif
				@endif

				@if (isset($parent))
					{{ __('') }}Ответ на <a href="#comment_{{ $parent->id }}">комментарий</a>
					от
					<x-user-name :user="$parent->create_user"/>
				@endif

			</div>
		</div>
	</div>
</div>

@include('comment/descendants', ['comment' => $item])


