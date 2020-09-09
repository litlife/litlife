<!--noindex-->
@if (!auth()->check())

	<div class="p-3">
		@include('auth.form')
	</div>
@else

	<div class="list-group mt-sm-3 shadow-sm rounded">

		<a class="list-group-item list-group-item-action @if (!empty(auth()->user()->thisPageInBookmarks)) d-none @else d-flex @endif pointer"
		   id="bookmarkAddButton" data-title="{{ \DaveJamesMiller\Breadcrumbs\Facades\Breadcrumbs::bookmarkTitle() }}"
		   data-user-id="{{ auth()->id() }}" data-toggle="modal"
		   data-target="#bookmarkAddModal">
                <span class="text-nowrap text-truncate">
                <i class="far fa-star"></i> {{ __('bookmark.create') }}
                </span>
		</a>

		<a class="list-group-item list-group-item-action @if (empty(auth()->user()->thisPageInBookmarks)) d-none @else d-flex @endif pointer"
		   id="bookmarkRemoveButton" href="javascript:void(0)"
		   data-toggle="modal" data-target="#bookmarkRemoveModal"
		   @if (!empty(auth()->user()->thisPageInBookmarks)) data-bookmark-id="{{ auth()->user()->thisPageInBookmarks->id }}" @endif>
                <span class="text-nowrap text-truncate">
                <i class="fas fa-star text-primary"></i>
                    <span class="folder-title @if (empty(auth()->user()->thisPageInBookmarks->folder)) d-none @endif">@if (!empty(auth()->user()->thisPageInBookmarks->folder))
							- {{ auth()->user()->thisPageInBookmarks->folder->title }} @endif</span>
                    <span class="remove-button-text @if (!empty(auth()->user()->thisPageInBookmarks->folder)) d-none @endif">{{ __('bookmark.remove') }}</span>
                </span>
		</a>

		<a class="list-group-item list-group-item-action d-flex {{ active('users/'.auth()->id().'/bookmarks') }}"
		   href="{{ route('users.bookmarks.index', auth()->user()) }}">

                    <span class="text-nowrap text-truncate ">
                        <i class="far fa-bookmark mr-1"></i> {{ __('navbar.bookmarks') }}
                    </span>
		</a>

		<a class="list-group-item list-group-item-action d-flex {{ active('users/'.auth()->id()) }}"
		   href="{{ route('profile', auth()->user()) }}">

                <span class="d-inline-flex text-nowrap text-truncate align-items-center">
					@if (auth()->user()->avatar)
						<x-user-avatar :user="auth()->user()" href="0" width="20" height="20" class="mr-2"/>
					@endif
					{{ auth()->user()->userName }}
                </span>
		</a>

		<a class="list-group-item list-group-item-action d-flex {{ active('news') }}" href="{{ route('news') }}"
		   title="{{ __('navbar.titles.news') }}" data-boundary="window" data-toggle="tooltip" data-placement="right">
			<span class="text-nowrap text-truncate">{{ __('navbar.news') }}</span>
			<span class="badge badge-primary badge-pill ml-auto">{{ empty($count = auth()->user()->getNotViewedFriendsNewsCount()) ? '' : $count }}</span>
		</a>

		@can('use_shop', \App\User::class)
			<a class="list-group-item list-group-item-action d-flex {{ active('users/'.auth()->id().'/wallet') }}"
			   href="{{ route('users.wallet', auth()->user()) }}"
			   data-boundary="window" data-toggle="tooltip" data-placement="right">
				<span class="text-nowrap text-truncate"><i class="fas fa-wallet"></i> {{ __('navbar.wallet') }}</span>
				<span class="badge badge-light badge-pill ml-auto">{{ empty($count = auth()->user()->balance()) ? '' : $count.' р.' }}</span>
			</a>
		@endcan

		<a class="list-group-item list-group-item-action d-flex {{ active('users/'.auth()->id().'/inbox') }}"
		   href="{{ route('users.inbox', auth()->user()) }}"
		   title="{{ __('navbar.titles.messages') }}" data-boundary="window" data-toggle="tooltip" data-placement="right">
			<span class="text-nowrap text-truncate"><i class="far fa-envelope mr-1"></i> {{ __('navbar.messages') }}</span>
			<span class="badge badge-primary badge-pill ml-auto">{{ empty($count = auth()->user()->getNewMessagesCount()) ? '' : $count }}</span>
		</a>

		<div class="list-group-item">
			<div class="collapsed d-flex pointer badge-fire-if-inner-badge-primary-exists" data-toggle="collapse"
				 href="#my_authors" role="button"
				 aria-expanded="true"
				 aria-controls="my_authors">
				<span class="flex-grow-1 text-nowrap text-truncate">{{ __('author.my_authors') }}</span>
				<span class="badge badge-primary badge-pill"></span>
				<span class="badge badge-lighmt badge-pill"><i class="fas fa-caret-down"></i></span>
			</div>
			<div class="collapse mt-3 {{ active(['authors.create', 'users.authors', 'users.authors.*'], 'show') }}"
				 id="my_authors">
				<div class="card">
					<div class="list-group list-group-flush ">

						<a class="list-group-item list-group-item-action d-flex {{ active('authors.create') }}"
						   href="{{ route('authors.create') }}">
                     <span class="text-nowrap text-truncate"><i
								 class="fas fa-plus"></i> {{ __('common.create') }}</span>
						</a>

						<a href="{{ route('users.authors', auth()->user()) }}"
						   class="list-group-item list-group-item-action d-flex {{ active('users/'.auth()->id().'/authors') }}">
                     <span class="text-nowrap text-truncate"><i
								 class="far fa-star"></i> {{ __('common.favorites') }}</span>
							<span class="badge badge-light badge-pill ml-auto">{{ auth()->user()->user_lib_author_count }}</span>
						</a>

						<a href="{{ route('users.authors.created', ['user' => auth()->user(), 'order' => 'created_at_desc']) }}"
						   class="list-group-item list-group-item-action d-flex {{ active('users/'.auth()->id().'/authors/created') }}">
							<span class="text-nowrap text-truncate">{{ trans_choice('common.created', 2) }}</span>
							@if (auth()->user()->data->created_authors_count)
								<span class="badge badge-light badge-pill ml-auto">{{ auth()->user()->data->created_authors_count }}</span>
							@endif
						</a>

						<a href="{{ route('users.authors.read_later', ['user' => auth()->user()]) }}"
						   class="list-group-item list-group-item-action d-flex {{ active('users/'.auth()->id().'/authors/read_later') }}">
							<span class="text-nowrap text-truncate">{{ trans_choice('user.my_read_status_array.read_later', auth()->user()->gender) }}</span>
						</a>

						<a href="{{ route('users.authors.read_now', ['user' => auth()->user()]) }}"
						   class="list-group-item list-group-item-action d-flex {{ active('users/'.auth()->id().'/authors/read_now') }}">
							<span class="text-nowrap text-truncate">{{ trans_choice('user.my_read_status_array.read_now', auth()->user()->gender) }}</span>
						</a>

						<a href="{{ route('users.authors.books', ['user' => auth()->user()]) }}"
						   title="{{ __('navbar.titles.my_authors.authors_books') }}" data-boundary="window"
						   data-toggle="tooltip"
						   data-placement="right"
						   class="list-group-item list-group-item-action d-flex {{ active('users/'.auth()->id().'/authors/books') }}">
							<span class="text-nowrap text-truncate">{{ __('user.authors_books') }}</span>
							<span class="badge badge-primary badge-pill ml-auto">{{ empty($count = auth()->user()->getNewFavoriteAuthorsBooksCount()) ? '' : $count }}</span>
						</a>
					</div>
				</div>
			</div>
		</div>

		<div class="list-group-item">
			<div class="collapsed  d-flex pointer" data-toggle="collapse" href="#my_books" role="button"
				 aria-expanded="true"
				 aria-controls="my_books">
				<span class="text-nowrap text-truncate">{{ __('book.my_books') }}</span>
				<span class="badge badge-light badge-pill ml-auto"><i class="fas fa-caret-down"></i></span>
			</div>
			<div class="collapse mt-3 {{ active(['users.books.purchased', 'books.create', 'users.books', 'users.books.created', 'users.books.readed',
                'users.books.read_later', 'users.books.read_now', 'users.books.not_read', 'users.votes', 'users.books.updates'], 'show') }}"
				 id="my_books">
				<div class="card">
					<div class="list-group list-group-flush ">

						<a href="{{ route('books.create') }}"
						   class="list-group-item list-group-item-action d-flex {{ active('books.create') }}">
                     <span class="text-nowrap text-truncate"><i
								 class="fas fa-plus"></i> {{ __('common.create') }}</span>
						</a>

						@can ('buy', \App\User::class)
							@if (auth()->user()->data->books_purchased_count)
								<a href="{{ route('users.books.purchased', ['user' => auth()->user(), 'order' => 'date_down']) }}"
								   class="list-group-item list-group-item-action d-flex {{ active('users/'.auth()->id().'/books/purchased') }}">
									<span class="text-nowrap text-truncate">{{ __('book.purchased') }}</span>
									<span class="badge badge-light badge-pill ml-auto">{{ auth()->user()->data->books_purchased_count }}</span>
								</a>
							@endif
						@endcan

						<a href="{{ route('users.books', auth()->user()) }}"
						   class="list-group-item list-group-item-action d-flex {{ active('users/'.auth()->id().'/books') }}">
                     <span class="text-nowrap text-truncate"><i
								 class="far fa-star"></i> {{ __('common.favorites') }}</span>
							<span class="badge badge-light badge-pill ml-auto">{{ auth()->user()->user_lib_book_count }}</span>
						</a>

						@if (!empty($count = auth()->user()->getFavoriteBooksWithUpdatesCount()))
							<a href="{{ route('users.books.updates', auth()->user()) }}"
							   title="{{ __('book.upadates_favorite_books') }}" data-boundary="window" data-toggle="tooltip"
							   data-placement="right"
							   class="list-group-item list-group-item-action d-flex {{ active('users/'.auth()->id().'/books/updates') }}">
								<span class="text-nowrap text-truncate"></i> {{ __('book.upadates_favorite_books') }}</span>
								<span class="badge badge-secondary badge-pill ml-auto">{{ $count }}</span>
							</a>
						@endif

						<a href="{{ route('users.books.created', ['user' => auth()->user(), 'order' => 'date_down']) }}"
						   class="list-group-item list-group-item-action d-flex {{ active('users/'.auth()->id().'/books/created') }}">
							<span class="text-nowrap text-truncate">{{ trans_choice('common.created', 2) }}</span>
							@if (auth()->user()->data->created_books_count)
								<span class="badge badge-light badge-pill ml-auto">{{ auth()->user()->data->created_books_count }}</span>
							@endif
						</a>

						<a href="{{ route('users.books.readed', ['user' => auth()->user()]) }}"
						   class="list-group-item list-group-item-action d-flex {{ active('users/'.auth()->id().'/books/readed') }}">
							<span class="text-nowrap text-truncate">{{ trans_choice('user.my_read_status_array.readed', auth()->user()->gender) }} </span>
							<span class="badge badge-light badge-pill ml-auto">{{ auth()->user()->book_read_count }}</span>
						</a>

						<a href="{{ route('users.books.read_later', ['user' => auth()->user()]) }}"
						   title="{{ __('navbar.titles.my_books.read_later') }}" data-boundary="window" data-toggle="tooltip"
						   data-placement="right"
						   class="list-group-item list-group-item-action d-flex {{ active('users/'.auth()->id().'/books/read_later') }}">
							<span class="text-nowrap text-truncate">{{ trans_choice('user.my_read_status_array.read_later', auth()->user()->gender) }}</span>
							<span class="badge badge-light badge-pill ml-auto">{{ auth()->user()->book_read_later_count }}</span>
						</a>

						<a href="{{ route('users.books.read_now', ['user' => auth()->user()]) }}"
						   class="list-group-item list-group-item-action d-flex {{ active('users/'.auth()->id().'/books/read_now') }}">
							<span class="text-nowrap text-truncate">{{ trans_choice('user.my_read_status_array.read_now', auth()->user()->gender) }}</span>
							<span class="badge badge-light badge-pill ml-auto">{{ auth()->user()->book_read_now_count }}</span>
						</a>

						<a href="{{ route('users.books.not_read', ['user' => auth()->user()]) }}"
						   class="list-group-item list-group-item-action d-flex {{ active('users/'.auth()->id().'/books/not_read') }}">
							<span class="text-nowrap text-truncate">{{ trans_choice('user.my_read_status_array.not_read', auth()->user()->gender) }}</span>
							<span class="badge badge-light badge-pill ml-auto">{{ auth()->user()->book_read_not_read_count }}</span>
						</a>

						<a href="{{ route("users.votes", ['user' => auth()->user(), 'order' => 'UserBookRateLast', 'view' => 'table']) }}"
						   class="list-group-item list-group-item-action d-flex {{ active('users/'.auth()->id().'/votes') }}">
							<span class="text-nowrap text-truncate">{{ __('navbar.book_votes') }}</span>
							<span class="badge badge-light badge-pill ml-auto">{{ auth()->user()->book_rate_count }}</span>
						</a>

						<a href="{{ route('topics.show', ['topic' => '211']) }}"
						   class="list-group-item list-group-item-action d-flex">
							<span class="text-nowrap text-truncate">{{ __('navbar.book_repeats') }}</span>
						</a>

					</div>
				</div>
			</div>
		</div>

		<div class="list-group-item">
			<div class="collapsed d-flex pointer" data-toggle="collapse" href="#my_sequences" role="button"
				 aria-expanded="true"
				 aria-controls="my_sequences">
				<span class="text-nowrap text-truncate">{{ __('sequence.my_sequences') }}</span>
				<span class="badge badge-light badge-pill ml-auto"><i class="fas fa-caret-down"></i></span>
			</div>
			<div class="collapse mt-3 {{ active(['sequences.create', 'users.sequences', 'users.sequences.created'], 'show') }}"
				 id="my_sequences">
				<div class="card">
					<div class="list-group list-group-flush ">

						<a class="list-group-item list-group-item-action d-flex {{ active('sequences.create') }}"
						   href="{{ route('sequences.create') }}">
                     <span class="text-nowrap text-truncate"><i
								 class="fas fa-plus"></i> {{ __('common.create') }}</span>
						</a>

						<a class="list-group-item list-group-item-action d-flex {{ active('users/'.auth()->id().'/sequences') }}"
						   href="{{ route('users.sequences', auth()->user()) }}">
                     <span class="text-nowrap text-truncate"><i
								 class="far fa-star"></i> {{ __('common.favorites') }}</span>
							<span class="badge badge-light badge-pill ml-auto">{{ auth()->user()->user_lib_sequence_count }}</span>
						</a>

						<a class="list-group-item list-group-item-action d-flex {{ active('users/'.auth()->id().'/sequences/created') }}"
						   href="{{ route('users.sequences.created', auth()->user()) }}">
							<span class="text-nowrap text-truncate">{{ trans_choice('common.created', 2) }}</span>
							@if (auth()->user()->data->created_sequences_count)
								<span class="badge badge-light badge-pill ml-auto">{{ auth()->user()->data->created_sequences_count }}</span>
							@endif
						</a>

					</div>
				</div>
			</div>
		</div>

		@can('use', App\Collection::class)
			<div class="list-group-item">
				<div class="collapsed d-flex pointer badge-fire-if-inner-badge-primary-exists" data-toggle="collapse"
					 href="#my_collections"
					 role="button"
					 aria-expanded="true"
					 aria-controls="my_collections">
					<span class="flex-grow-1 text-nowrap text-truncate">{{ __('sidebar.my_collections') }}</span>
					<span class="badge badge-primary badge-pill"></span>
					<span class="badge badge-lighmt badge-pill"><i class="fas fa-caret-down"></i></span>
				</div>
				<div id="my_collections" class="collapse mt-3">
					<div class="card">
						<div class="list-group list-group-flush ">

							<a class="list-group-item list-group-item-action d-flex {{ active('collections/create') }}"
							   href="{{ route('collections.create') }}">
                        <span class="text-nowrap text-truncate"><i
									class="fas fa-plus"></i> {{ __('common.create') }}</span>
							</a>

							<a class="list-group-item list-group-item-action d-flex {{ active('users/'.auth()->id().'/collections/created') }}"
							   href="{{ route('users.collections.created', auth()->user()) }}">
								<span class="text-nowrap text-truncate">{{ __('sidebar.created_collections') }}</span>
								<span class="badge badge-light badge-pill ml-auto">{{ auth()->user()->data->created_collections_count }}</span>
							</a>

							<a class="list-group-item list-group-item-action d-flex {{ active('users/'.auth()->id().'/collections/favorite') }}"
							   href="{{ route('users.collections.favorite', auth()->user()) }}">
								<span class="text-nowrap text-truncate"><i class="far fa-star"></i> {{ __('sidebar.favorite_collections') }}</span>
								<span class="badge badge-light badge-pill ml-auto">{{ auth()->user()->data->favorite_collections_count }}</span>
							</a>
						</div>
					</div>
				</div>
			</div>
		@endcan

		<a class="list-group-item list-group-item-action d-flex {{ active('users/'.auth()->id().'/notifications') }}"
		   href="{{ route('users.notifications.index', ['user' => auth()->user()]) }}"
		   title="" data-boundary="window" data-toggle="tooltip" data-placement="right">
         <span class="text-nowrap text-truncate"><i
					 class="far fa-bell"></i> {{ __('notification.notification') }}</span>

			@if ($count = auth()->user()->getUnreadNotificationsCount())
				<span class="badge badge-primary badge-pill ml-auto">{{ $count }}</span>
			@endif
		</a>


		<a class="list-group-item list-group-item-action d-flex {{ active(['users/'.auth()->id().'/notes*', 'notes*']) }}"
		   href="{{ route('users.notes.index', auth()->user()) }}">
			<span class="text-nowrap text-truncate">{{ __('navbar.notes') }}</span>
		</a>

		@if (!empty(auth()->user()->achievements_count))
			<a class="list-group-item list-group-item-action d-flex {{ active('users/'.auth()->id().'/achievements') }}"
			   href="{{ route('users.achievements', auth()->user()) }}"
			   title="{{ __('navbar.titles.achievements') }}" data-boundary="window" data-toggle="tooltip"
			   data-placement="right">
				<span class="text-nowrap text-truncate">{{ __('navbar.my_achievements') }}</span>
				<span class="badge badge-light badge-pill ml-auto">{{ auth()->user()->achievements_count }}</span>
			</a>
		@endif

		<a href="{{ route('users.subscriptions.comments', auth()->user()) }}"
		   title="{{ __('navbar.titles.subscriptions_comments') }}" data-boundary="window" data-toggle="tooltip"
		   data-placement="right"
		   class="list-group-item list-group-item-action d-flex {{ active('users/'.auth()->id().'/subscriptions/comments') }}">
			<span class="text-nowrap text-truncate">{{ __('user.subscriptions_comments') }}</span>
		</a>

		<a href="{{ route('users.books.readed.comments', auth()->user()) }}"
		   title="{{ __('user.comments_readed_books') }}" data-boundary="window" data-toggle="tooltip"
		   data-placement="right"
		   class="list-group-item list-group-item-action d-flex {{ active('users/'.auth()->id().'/books/readed/comments') }}">
			<span class="text-nowrap text-truncate">{{ __('user.comments_readed_books') }}</span>
		</a>

		<div class="list-group-item show-if-list-group-item-exists-inside  d-none">
			<a class="collapsed d-flex pointer text-decoration-none badge-fire-if-inner-badge-primary-exists"
			   data-toggle="collapse"
			   href="#admin_functions" role="button" aria-expanded="true"
			   aria-controls="admin_functions">
				<span class="text-nowrap text-truncate">{{ __('navbar.admin_functions') }}</span>
				<span class="count badge badge-primary badge-pill ml-auto"></span>
				<span class="badge badge-light badge-pill ml-auto"><i class="fas fa-caret-down"></i></span>
			</a>
			<div class="collapse mt-3 {{ active(['users.on_moderation', 'books.on_moderation', 'book_files.on_moderation', 'complaints.index', 'book_keywords.on_moderation', 'managers.on_check', 'posts.on_check', 'comments.on_check', 'settings.index', 'achievements.index', 'groups.index', 'books.trashed'], 'show') }}"
				 id="admin_functions">
				<div class="card">
					<div class="list-group list-group-flush">

						@can ('view_on_moderation', auth()->user())
							<a href="{{ route('users.on_moderation') }}"
							   title="{{ __('navbar.users_on_moderation') }}" data-boundary="window" data-toggle="tooltip"
							   data-placement="right"
							   class="list-group-item list-group-item-action d-flex {{ active('users.on_moderation') }}">
								<span class="text-nowrap text-truncate">{{ __('navbar.users_on_moderation') }}</span>
								@if ($count = App\UserOnModeration::getCachedCount())
									<span class="badge badge-light badge-pill ml-auto">{{ $count }}</span>
								@endif
							</a>
						@endcan

						@can ('view_on_moderation', App\Book::class)
							@if ($count = \App\Book::getCachedOnModerationCount())
								<a class="list-group-item list-group-item-action d-flex {{ active('books.on_moderation') }}"
								   href="{{ route('books.on_moderation') }}">
									<span class="text-nowrap text-truncate">{{ __('navbar.books_on_check') }}</span>
									<span class="badge badge-primary badge-pill ml-auto">{{ $count }}</span>
								</a>
							@endif
						@endcan

						@can ('view_on_moderation', App\BookFile::class)
							@if ($count = \App\BookFile::getCachedOnModerationCount())
								<a class="list-group-item list-group-item-action d-flex {{ active('book_files.on_moderation') }}"
								   title="{{ __('navbar.book_files_on_check') }}" data-boundary="window" data-toggle="tooltip"
								   data-placement="right"
								   href="{{ route('book_files.on_moderation') }}">
									<span class="text-nowrap text-truncate">{{ __('navbar.book_files_on_check') }}</span>
									<span class="badge badge-primary badge-pill ml-auto">{{ $count }}</span>
								</a>
							@endif
						@endcan

							@can ('viewOnReviewList', App\Complain::class)

							<a href="{{ route('complaints.index') }}"
							   class="list-group-item list-group-item-action d-flex {{ active('complaints.index') }}">
								<span class="text-nowrap text-truncate">{{ __('navbar.complains') }}</span>
								@if ($count = \App\Complain::getCachedOnModerationCount())
									<span class="badge badge-primary badge-pill ml-auto">{{ $count }}</span>
								@endif
							</a>
						@endcan

						@can ('viewOnCheck', App\BookKeyword::class)
							@if ($count = App\BookKeyword::getCachedOnModerationCount())
								<a href="{{ route('book_keywords.on_moderation') }}"
								   title="{{ __('navbar.keyword_on_moderation') }}" data-boundary="window" data-toggle="tooltip"
								   data-placement="right"
								   class="list-group-item list-group-item-action d-flex {{ active('book_keywords.on_moderation') }}">
									<span class="text-nowrap text-truncate">{{ __('navbar.keyword_on_moderation') }}</span>
									<span class="badge badge-primary badge-pill ml-auto">{{ $count }}</span>
								</a>
							@endif
						@endcan

						@can ('viewOnCheck', App\Manager::class)
							<a href="{{ route('managers.on_check') }}"
							   title="{{ __('Requests for verification') }}" data-boundary="window" data-toggle="tooltip"
							   data-placement="right"
							   class="list-group-item list-group-item-action d-flex {{ active('managers.on_check') }}">
								<span class="text-nowrap text-truncate">{{ __('Requests for verification') }}</span>
								@if ($count = App\Manager::getCachedOnModerationCount())
									<span class="badge badge-primary badge-pill ml-auto">{{ $count }}</span>
								@endif
							</a>
						@endcan

						@can ('author_sale_request_review', \App\User::class)
							<a href="{{ route('authors.sales_requests.index') }}"
							   title="{{ __('navbar.author_sale_request') }}" data-boundary="window" data-toggle="tooltip"
							   data-placement="right"
							   class="list-group-item list-group-item-action d-flex {{ active('authors.sales_requests.index') }}">
								<span class="text-nowrap text-truncate">{{ __('navbar.author_sale_request') }}</span>
								@if ($count = App\AuthorSaleRequest::getCachedOnModerationCount())
									<span class="badge badge-primary badge-pill ml-auto">{{ $count }}</span>
								@endif
							</a>
						@endcan

						@if ($count = App\Comment::getCachedOnModerationCount())
							@can ('viewOnCheck', App\Comment::class)
								<a href="{{ route('comments.on_check') }}"
								   class="list-group-item list-group-item-action d-flex {{ active('comments.on_check') }}">
									<span class="text-nowrap text-truncate">{{ __('navbar.comments_on_check') }}</span>
									<span class="badge badge-primary badge-pill ml-auto">{{ $count }}</span>
								</a>
							@endcan
						@endif

						@if ($count = App\Post::getCachedOnModerationCount())
							@can ('viewOnCheck', App\Post::class)
								<a href="{{ route('posts.on_check') }}"
								   title="{{ __('navbar.forum_posts_on_check') }}" data-boundary="window" data-toggle="tooltip"
								   data-placement="right"
								   class="list-group-item list-group-item-action d-flex {{ active('posts.on_check') }}">
									<span class="text-nowrap text-truncate">{{ __('navbar.forum_posts_on_check') }}</span>
									<span class="badge badge-primary badge-pill ml-auto">{{ $count }}</span>
								</a>
							@endcan
						@endif

						@if ($count = App\Blog::getCachedOnModerationCount())
							@can ('viewOnCheck', App\Blog::class)
								<a href="{{ route('wall_posts.on_review') }}"
								   class="list-group-item list-group-item-action d-flex">
									<span class="text-nowrap text-truncate">{{ __('navbar.wall_posts_on_check') }}</span>
									<span class="badge badge-primary badge-pill ml-auto">{{ $count }}</span>
								</a>
							@endcan
						@endif

						@can ('admin_panel_access', App\User::class)
							<a href="{{ route('settings.index') }}"
							   class="list-group-item list-group-item-action d-flex {{ active('settings.index') }}">
								<span class="text-nowrap text-truncate">{{ __('navbar.site_settings') }}</span>
							</a>
						@endcan

						@can ('create', App\Achievement::class)
							<a href="{{ route('achievements.index') }}"
							   class="list-group-item list-group-item-action d-flex {{ active('achievements.index') }}">
								<span class="text-nowrap text-truncate">{{ __('navbar.achievements') }}</span>
							</a>
						@endcan

						@can ('view', App\UserGroup::class)
							<a href="{{ route('groups.index') }}"
							   class="list-group-item list-group-item-action d-flex {{ active('groups.index') }}">
								<span class="text-nowrap text-truncate">{{ __('navbar.user_groups') }}</span>
							</a>
						@endcan

						@can ('viewAtSidebar', App\Keyword::class)
							<a href="{{ route('keywords.index') }}"
							   class="list-group-item list-group-item-action d-flex {{ active('keywords.index') }}">
								<span class="text-nowrap text-truncate">{{ trans_choice('keyword.keywords', 2) }}</span>
							</a>
						@endcan

						@can ('merge', \App\Author::class)
							<a href="{{ route('author_repeats.index') }}"
							   class="list-group-item list-group-item-action d-flex  {{ active('author_repeats.index') }}">
								<span class="text-nowrap text-truncate">{{ __('navbar.author_repeats') }}</span>
								@if ($count = App\AuthorRepeat::getCachedOnModerationCount())
									<span class="badge badge-primary badge-pill ml-auto">{{ $count }}</span>
								@endif
							</a>
						@endcan

						@can ('view_deleted', App\Book::class)
							<a href="{{ route('books.trashed') }}"
							   class="list-group-item list-group-item-action d-flex {{ active('books.trashed') }}">
								<span class="text-nowrap text-truncate">{{ __('book.trashed_books') }}</span>
							</a>
						@endcan

						@can ('manage_mailings', App\User::class)
							<a href="{{ route('mailings.index') }}"
							   class="list-group-item list-group-item-action d-flex {{ active('mailings.index') }}">
								<span class="text-nowrap text-truncate">{{ __('user_group.manage_mailings') }}</span>
							</a>
						@endcan

							@can ('viewUserSurveys', App\User::class)
								<a href="{{ route('surveys.index') }}"
								   class="list-group-item list-group-item-action d-flex {{ active('surveys.index') }}">
									<span class="text-nowrap text-truncate">{{ __('survey.survey_result') }}</span>
								</a>
							@endcan
					</div>
				</div>
			</div>
		</div>

		<script type="text/javascript">

			document.addEventListener('DOMContentLoaded', function () {

				$('.show-if-list-group-item-exists-inside').each(function () {
					var item = $(this);

					if (item.find('.list-group-item').length > 0)
						item.removeClass('d-none');
				});
			});

		</script>

		<a href="{{ route('allowance', auth()->user()) }}"
		   class="list-group-item list-group-item-action d-flex {{ active('users/'.auth()->id().'/settings/allowance') }}">
			<span class="text-nowrap text-truncate"><i class="fas fa-cogs"></i> {{ __('navbar.settings') }}</span>
		</a>

		<a href="{{ route('logout') }}"
		   class="list-group-item list-group-item-action d-flex {{ active('logout') }}">
			<span class="text-nowrap text-truncate">{{ __('navbar.logout') }}</span>
		</a>

	</div>

	@auth
		@push('body_append')
			<div id="bookmarkAddModal" class="modal" tabindex="-1" role="dialog">
				<div class="modal-dialog" role="document">
					<div class="modal-content">
						<div class="modal-header">
							<h5 class="modal-title" id="bookmarkAddModalLabel">{{ __('bookmark.create') }}</h5>
							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
								<span aria-hidden="true">&times;</span>
							</button>
						</div>
						<form id="bookmarkAddModal-form" action="{{ route('bookmarks.store') }}" role="form"
							  method="POST">
							<div class="modal-body">
								<div class="form-group">
									<div class="col-12">
                                            <textarea name="title" class="form-control"
													  placeholder="{{ __('bookmark.title') }}"
													  maxlength="250" required></textarea>
										<small id="titleHelp"
											   class="form-text text-muted">{{ __('bookmark.title_helper') }}</small>
									</div>
								</div>
								<div class="form-group">
									<div class="col-12">
										<select name="folder" class="form-control"></select>
									</div>
								</div>
							</div>
							<div class="modal-footer">
								<button type="button" class="btn btn-light"
										data-dismiss="modal">{{ __('common.close') }}</button>
								<button type="submit" class="submit btn btn-primary">{{ __('common.add') }}</button>
							</div>

						</form>
					</div>
				</div>
			</div>

			<div class="modal" id="bookmarkRemoveModal" tabindex="-1" role="dialog" aria-labelledby="bookmarkRemoveModal">
				<div class="modal-dialog" role="document">
					<div class="modal-content">
						<div class="modal-header">
							<h5 class="modal-title" id="bookmarkRemoveModal">{{ __('bookmark.remove') }}</h5>
							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
								<span aria-hidden="true">&times;</span>
							</button>
						</div>
						<div class="modal-body">
							<p>{{ __('bookmark.delete_confirm') }}</p>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-light" data-dismiss="modal">{{ __('common.close') }}</button>
							<button type="button" class="submit btn btn-primary">{{ __('common.delete') }}</button>
						</div>
					</div>
				</div>
			</div>

		@endpush
	@endauth
@endif
<!--/noindex-->

@if (!session()->get('dont_show_idea_card'))

	<div class="idea-card card mt-3 mb-3 mx-3">
		<button type="button" class="card-close close mr-1" data-dismiss="modal" aria-label="Close">
			<span class="text-muted" aria-hidden="true">&times;</span>
		</button>

		<div class="card-body d-flex flex-column text-center mt-0 p-2">
			<p class="card-text mb-2 mx-3">
				<small>{{ __('idea.help_us_make_a_better') }}</small>
			</p>

			<a href="{{ route('ideas.index') }}" class="btn btn-sm btn-primary">
				<i class="far fa-lightbulb"></i> {{ __('idea.suggest_idea') }}
			</a>
		</div>
	</div>

@endif
