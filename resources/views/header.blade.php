<header class="navbar navbar-expand navbar-dark bg-primary flex-column navbar-horizontal-scroll">

	<div class="container-fluid" style="max-width: 1200px;">

		<table class="navbar-nav">
			<tr>
				<td>
					@if (empty($menu_disable))
						<div class="nav-item mr-3 mt-1">
							<button id="sidebar-toggle" class="btn btn-primary text-nowrap" type="button" data-target="#sidebar"
									data-placement="bottom" title="{{ __('navbar.user_menu') }}">
								<i class="fas fa-bars"></i>
								<span class="number-of-all-unread-notifications badge badge-light" style="display: none;"></span>
							</button>
						</div>
					@endif

				</td>
				<td>
					<a class="navbar-brand" href="{{ url('/') }}" title="{{ __('home.go_to') }}">
						<div class="logo-white d-inline-block align-top"
							 style="width:2.2rem; height:2rem; background-size:contain;"></div>
						<h1 class="ml-1 d-none d-sm-inline h5">
							{{ __('app.name_first_part') }}<b>{{ __('app.name_second_part') }}</b>
						</h1>
					</a>
				</td>
				<td>
					<div class="nav-item">

						<a href="{{ route('genres') }}"
						   class="nav-link text-nowrap {{ isActiveRoute('genres') }}" role="button"
						   @if (cache('genres_count') > 0) title="{{ __('header.genres') }}: {{ cache('genres_count') }}" @endif>
							<h2 class="h6 d-inline font-weight-normal">
								{{ __('navbar.genres') }}
							</h2>
						</a>
					</div>
				</td>
				<td>
					<div class="nav-item">
						<a class="nav-link text-nowrap {{ isActiveRoute('authors') }}" href="{{ route('authors') }}"
						   @if (cache('authors_count') > 0) title="{{ __('header.authors') }}: {{ cache('authors_count') }}" @endif>
							<h2 class="h6 d-inline  font-weight-normal">
								{{ __('navbar.all_authors') }}
							</h2>
						</a>
					</div>
				</td>
				<td>
					<div class="nav-item">
						<a class="nav-link text-nowrap {{ isActiveRoute('books') }}"
						   href="{{ route('books', ['order' => 'rating_week_desc']) }}"
						   @if (cache('books_count') > 0) title="{{ __('header.books') }}: {{ cache('books_count') }}" @endif>
							<i class="fas fa-book"></i>
							<h2 class="h6 d-inline  font-weight-normal"> {{ __('navbar.all_books') }}</h2>
						</a>
					</div>
				</td>
				<td>
					<div class="nav-item">
						<a class="nav-link text-nowrap {{ isActiveRoute('sequences') }}" href="{{ route('sequences') }}"
						   @if (cache('sequences_count') > 0) title="{{ __('header.sequences') }}: {{ cache('sequences_count') }}" @endif>
							<h2 class="h6 d-inline font-weight-normal">
								{{ __('navbar.all_sequences') }}
							</h2>
						</a>
					</div>
				</td>
				<td>
					<div class="nav-item">
						<a class="nav-link text-nowrap {{ isActiveRoute('forums.index') }}" href="{{ route('forums.index') }}"
						   @if (cache('posts_count') > 0) title="{{ __('header.posts') }}: {{ cache('posts_count') }}" @endif>
							<i class="fas fa-comments"></i>
							<h2 class="h6 d-inline font-weight-normal">{{ __('navbar.forum') }}</h2>
						</a>
					</div>
				</td>
				<td>
					<div class="nav-item">
						<a class="nav-link text-nowrap {{ isActiveRoute('collections.index') }}"
						   title="{{ __('header.collections_title') }}"
						   href="{{ route('collections.index', ['order' => 'likes_count_desc']) }}">
							{{ __('header.collections') }}
						</a>
					</div>
				</td>
				<td>
					<div class="nav-item">
						<a class="nav-link text-nowrap {{ isActiveRoute('users') }}" href="{{ route('users') }}"
						   title="{{ __('header.users') }}: {{ cache('users_count') }} @if (cache('users_online_count') > 0) {{ __('header.users_online') }}: {{ cache('users_online_count') }} @endif">
							<i class="fas fa-users"></i>
						</a>
					</div>
				</td>

				@can('create_support_questions', auth()->user())
					<td>
						<div class="nav-item">
							<a class="nav-link text-nowrap"
							   href="{{ route('support') }}">
								{{ __('Support') }}
							</a>
						</div>
					</td>
				@endcan

				<td>
					<div class="nav-item">

						<form id="search_outter_form" class="ml-1 mt-1" action="{{ route('search') }}">
							<div class="input-group input-group-sm d-flex flex-nowrap">
								<input name="query" type="text" required
									   minlength="{{ config('litlife.minimum_number_of_letters_and_numbers') }}"
									   class="form-control form-control" placeholder="{{ __('search.placeholder') }}"
									   style="min-width:190px;"
									   value="">
								<div class="input-group-append">
									<button type="submit" class="btn btn-secondary">
										<i class="fas fa-search"></i>
									</button>
								</div>
							</div>
						</form>

						@push('body_append')

							<div class="modal" id="common_search_modal" tabindex="-1" role="dialog" aria-labelledby="searchModal">
								<div class="modal-dialog modal-lg" role="document">
									<div class="modal-content">
										<div class="modal-header">
											<h5 class="modal-title">{{ __('search.result_of_search') }}: <span class="title_query"></span></h5>
											<button type="button" class="close" data-dismiss="modal" aria-label="Close">
												<span aria-hidden="true">&times;</span>
											</button>
										</div>

										<div class="modal-header">
											<form class="w-100" action="{{ route('search') }}">
												<div class="form-group mb-0">
													<input name="query" type="text" required
														   minlength="{{ config('litlife.minimum_number_of_letters_and_numbers') }}"
														   class="form-control" placeholder="{{ __('search.placeholder') }}" value="">
												</div>
											</form>
										</div>

										<div class="result">
											<h1 class="spinner text-center py-5">
												<i class="fas fa-spinner fa-spin"></i>
											</h1>
										</div>
									</div>
								</div>
							</div>

						@endpush

					</div>
				</td>
				<td>
					<div class="nav-item ml-1">
						<a class="nav-link" title="{{ __('navbar.qrcode') }}" style="cursor: pointer;">
							<i data-toggle="modal" data-target="#QRCodeDialog" class="fas fa-qrcode"></i>
						</a>
					</div>
				</td>
			</tr>
		</table>
	</div>


	@push('body_append')
		<div class="modal" id="QRCodeDialog" tabindex="-1" role="dialog" aria-labelledby="QRCodeRemoveModal" data-url="{{ route('qrcode') }}">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="QRCodeRemoveModal">{{ __('navbar.qrcode') }}</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body" style="text-align: center; font-size:36px;">
						<i class="fas fa-spinner fa-spin"></i>
					</div>
				</div>
			</div>
		</div>
	@endpush

</header>

