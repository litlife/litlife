@extends('layouts.app')

@push('scripts')
	<script src="{{ mix('js/books.show.js', config('litlife.assets_path')) }}"></script>
	<script src="//cdnjs.cloudflare.com/ajax/libs/jplayer/2.9.2/jplayer/jquery.jplayer.min.js"></script>
@endpush

@push('scripts')

	<div id="ask_user_to_rate_the_book_modal" class="modal" role="dialog" aria-modal="true" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h6 class="modal-title">{{ __('book.your_assessment_of_the_book') }}</h6>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">

					<ul class="list-group">
						@foreach (config('litlife.votes') as $vote)
							<a class="list-group-item list-group-item-action font-weight-bold @if (isset($user_book_vote) and $user_book_vote->vote == $vote)
									active
@endif"
							   href="{{ route('books.vote', ['book' => $book, 'vote' => $vote]) }}"
							   data-vote="{{ $vote }}">
								{{ __('book.vote_descriptions.'.$vote) }}
							</a>
						@endforeach
					</ul>

				</div>
			</div>
		</div>
	</div>

	@isset($ask_user_to_rate_the_book)
		<script type="text/javascript">
			$(window).on('load', function () {
				$('#ask_user_to_rate_the_book_modal').modal('show');
			});
		</script>
	@endisset

@endpush

@push ('css')

@endpush

@section('content')

@section('top')

	@if (!$book->parse->isSucceed())
		<div class="mb-3">
			@if ($book->parse->isWait())
				<div class="alert alert-info" role="alert">
					{{ __('book.parse.wait') }} {{ __('book.check_later') }}
				</div>
			@elseif ($book->parse->isProgress())
				<div class="alert alert-warning" role="alert">
					{{ __('book.parse.progress') }} {{ __('book.check_later') }}
				</div>
			@elseif ($book->parse->isFailed())

				<div class="alert alert-danger" role="alert">
					{{ __('book.parse.failed') }}
				</div>

				@if (!empty($book->parse->parse_errors))
					<div class="alert alert-danger" role="alert">
						<b>{{ __('common.error') }}:</b>
						{{ $book->parse->parse_errors['message'] }}
					</div>
				@endif

				@can('retry_failed_parse', $book)
					<a href="{{ route('books.retry_failed_parse', $book) }}"
					   class="btn btn-primary mb-3">{{ __('book.retry_failed_parse') }}</a>
				@endcan

			@endif

			@can('cancel_parse', $book)
				<a href="{{ route('books.cancel_parse', $book) }}"
				   class="btn btn-primary">{{ __('book.cancel_parse') }}</a>
			@endcan
		</div>
	@endif

	@if ($book->isSentForReview())
		<div class="alert alert-info" role="alert">
			{{ __('book.on_check') }}
			@if ($book->isUserChangedStatus(auth()->user()))
				{{ __('book.you_will_receive_a_notification_when_the_book_is_published') }}
			@endif
		</div>
	@endif

	@if ($book->isPrivate() and $book->isForSale())
		<div class="mb-3">
			<div class="alert alert-warning">
				{{ __('book.for_the_book_to_start_being_sold_you_must_publish_it') }}
			</div>

			<a class="btn btn-primary"
			   href="{{ route('books.publish', ['book' => $book->id]) }}">{{ __('book.publish_a_book') }}</a>
		</div>
	@endif

	@if (!$book->isReadAccess() and !$book->isDownloadAccess())
		@can ('change_access', $book)
			@if ($book->isUserVerifiedAuthorOfBook(auth()->user()))
				<div class="alert alert-warning">
					{{ __('book.access_to_reading_and_downloading_book_files_is_currently_closed') }}
					<a class="alert-link" href="{{ route('books.access.edit', $book) }}">
						{{ __('book.click_here_to_open_access') }}
					</a>
				</div>
			@endif
		@endcan
	@endif

	@if ($book->isRejected())
		<div class="alert alert-warning" role="alert">
			{{ __('book.removed_from_sale') }}
		</div>
	@endif
@show

@section('alerts')
	@if (session('success'))
		<div class="alert alert-success alert-dismissable">
			{{ session('success') }}
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		</div>
	@endif

	@if (session('is_created'))
		<div class="alert alert-info" role="alert">
			<p>{{ __('book.the_book_is_in_your_personal_access_this_is_indicated_by_the_lock_icon_next_to_the_title_of_the_book') }}</p>
			<p>{{ __('book.if_you_want_the_book_to_be_visible_to_everyone_you_need_to_publish_the_book') }}</p>
			<p>{{ __('book.to_publish_a_book_open_the_book_menu_and_click_publish') }}</p>
		</div>
	@endif

	@if ($errors->publish->any())
		<div class="alert alert-danger">
			{!! __('book.fix_errors_before_publish', ['link' => route('books.edit', $book)]) !!}:
			<ul>
				@foreach ($errors->publish->all() as $error)
					<li>{{ $error }}</li>
				@endforeach
			</ul>
		</div>
	@endif

	@if ($errors->buy->any())
		<div class="alert alert-danger">
			<ul>
				@foreach ($errors->buy->all() as $error)
					<li>{{ $error }}</li>
				@endforeach
			</ul>
		</div>
	@endif

	@if ($errors->any())
		<div class="alert alert-danger">
			<ul>
				@foreach ($errors->all() as $error)
					<li>{{ $error }}</li>
				@endforeach
			</ul>
		</div>
	@endif
@show

<div class="book row" itemscope="" itemtype="http://schema.org/Book">
	<meta itemprop="isbn" content="{{ $book->pi_isbn }}"/>
	<meta itemprop="bookEdition" content="{{ $book->redaction }}"/>

	<div class="col-md-4 col-lg-3">

		@section('cover')
			<div class="card mb-3">
				<div class="card-body text-center cover">
					<x-book-cover :book="$book"
								  href="{{ route('books.cover.show', ['book' => $book]) }}"
								  width="200" height="400" style="max-width: 100%;" :showEvenIfTrashed="$show_even_if_trashed ?? null"/>
				</div>
			</div>
		@show

		@section('similar')
			@if (!$book->trashed() and !$book->isPrivate())

				<div class="card mb-3">
					<div class="card-header text-center">
						<small>{{ __('book.same_books') }}:</small>

						<button type="button" class="d-md-none btn btn-light btn-sm" data-toggle="collapse"
								data-target="#collapse-similar-books"
								aria-expanded="false"
								aria-controls="collapse-similar-books">{{ __('common.toggle') }}</button>
					</div>

					<div id="collapse-similar-books" class="card-body collapse dont-collapse-sm p-0">
						@if ((!empty($books_similar)) and $books_similar->count())

							<div class="list-group list-group-flush mb-3">

								@foreach ($books_similar as $book_similar)

									<div class="similars_item list-group-item text-center"
										 data-other-book-id="{{ $book_similar->other_book_id }}">

										<div class="mb-2 text-center">
											<x-book-cover :book="$book_similar" width="100" height="100" style="max-width: 100%;"/>
										</div>

										@if ($book_similar->isAccepted())
											<div class="small">
												{{ __('book.vote_average') }}: {{ round($book_similar->vote_average, 2) }}
												<a href="{{ route('books.votes', $book_similar) }}">
													({{ $book_similar->user_vote_count }})
												</a>
											</div>
										@endif
										<div class="small">
											<div>
												<x-book-name :book="$book_similar"/>
											</div>
											<div>
												@if ((isset($book_similar->writers)) and ($book_similar->writers->count()))
													@foreach ($book_similar->writers as $author)
														<x-author-name :author="$author"/>{{ $loop->last ? '' : ', ' }}
													@endforeach
												@endif
											</div>
											<div class="btn-group" role="group">
												<button
														class="similar btn btn-sm btn-light @if (isset($book_similar->similar_vote[0]) and ($book_similar->similar_vote[0]->vote > 0)) active @endif"
														data-toggle="button">
													{{ __('book.same') }}
												</button>

												<button
														class="not_similar btn btn-sm btn-light @if (isset($book_similar->similar_vote[0]) and ($book_similar->similar_vote[0]->vote < 0)) active @endif"
														data-toggle="button">
													{{ __('book.not_same') }}
												</button>
											</div>
										</div>
									</div>
								@endforeach

							</div>

						@endif

						<div class="text-center px-2">
							<h6>{{ __('book.attach_similar_book') }}</h6>
						</div>

						<div class="text-center px-2 pb-2">
							<form action="{{ route('books.similar.create', compact('book')) }}" method="post">
								@csrf

								@if ($errors->similar_vote->any())
									<div class="alert alert-danger">
										<ul>
											@foreach ($errors->similar_vote->all() as $error)
												<li>{{ $error }}</li>
											@endforeach
										</ul>
									</div>
								@endif

								<div class="form-group pl-4 pr-4 mb-1">
									<input id="book_id" name="book_id" type="text"
										   placeholder="{{ __('book_similar_vote.book_id') }}"
										   class="form-control{{ $errors->similar_vote->has('book_id') ? ' is-invalid' : '' }}"
										   value="{{ old('book_id') }}"/>
								</div>
								<button type="submit" class="btn btn-light">{{ __('common.add') }}</button>
							</form>
						</div>

					</div>
				</div>



				@if (!empty($rand_books) and ($rand_books->count() > 0))

					<div class="card mb-3">
						<div class="card-header text-center">

							<small>{{ __('book.same_genres_books') }}</small>

							<button type="button" class="d-md-none btn btn-light btn-sm" data-toggle="collapse"
									data-target="#collapse-rand-books"
									aria-expanded="false"
									aria-controls="collapse-rand-books">{{ __('common.toggle') }}</button>

						</div>

						<div id="collapse-rand-books" class="collapse dont-collapse-sm card-body p-0">

							<div class="list-group list-group-flush mb-3 text-center">

								@foreach ($rand_books as $rand_book)

									<div class="list-group-item">
										<div class="mb-2 text-center">
											<x-book-cover :book="$rand_book" width="100" height="100" style="max-width: 100%;"/>
										</div>

										@if ($rand_book->isAccepted())
											<div class="small">
												{{ __('book.vote_average') }}: {{ round($rand_book->vote_average, 2) }}
												<a href="{{ route('books.votes', $rand_book) }}">
													({{ $rand_book->user_vote_count }})
												</a>
											</div>
										@endif

										<div class="small">
											<x-book-name :book="$rand_book"/>
										</div>
										<div class="small">
											@if ((isset($rand_book->writers)) and ($rand_book->writers->count()))
												@foreach ($rand_book->writers as $author)
													<x-author-name :author="$author"/> {{ $loop->last ? '' : ', ' }}
												@endforeach
											@endif
										</div>
									</div>
								@endforeach
							</div>
						</div>
					</div>
				@endif
			@endif
		@show

	</div>
	<div class="col-md-8 col-lg-9">

		<div class="card  mb-3">
			<div class="card-header">
				<div class="d-flex w-100 justify-content-between">
					<h2 class="title break-word inline h5" itemprop="name">
						<x-book-name :book="$book" href="0" :showEvenIfTrashed="$show_even_if_trashed ?? null"/>
					</h2>
					<div class="ml-auprogressto">
						<div class="btn-group" data-toggle="tooltip" data-placement="top"
							 title="{{ __('common.open_actions') }}">
							<button class="btn btn-light dropdown-toggle" type="button" id="bookDropdownMenuButton"
									data-toggle="dropdown"
									aria-haspopup="true"
									aria-expanded="false">
								<i class="fas fa-ellipsis-h"></i>
							</button>

							<div class="dropdown-menu dropdown-menu-right" aria-labelledby="bookDropdownMenuButton">

								@can('update', $book)
									<a class="dropdown-item text-lowercase" href="{{ route('books.edit', $book) }}">
										{{ __('common.edit') }}
									</a>
								@endcan

								@can('publish', $book)
									<a class="dropdown-item text-lowercase"
									   href="{{ route('books.publish', $book) }}">
										{{ __('book.publish') }}
									</a>
								@endcan

								@can('group', $book)
									<a class="dropdown-item text-lowercase"
									   href="{{ route('books.editions.edit', $book) }}">
										{{ __('book.group') }}
									</a>
								@endcan

								@can('delete', $book)
									@if ($book->isPrivate())
										<a class="dropdown-item text-lowercase" href="{{ route('books.delete', $book) }}">
											{{ __('common.delete') }}
										</a>
									@else
										<a class="dropdown-item text-lowercase" href="{{ route('books.delete.form', $book) }}">
											{{ __('common.delete') }}
										</a>
									@endif
								@elsecan('restore', $book)
									<a class="dropdown-item text-lowercase" href="{{ route('books.restore', $book) }}">
										{{ __('common.restore') }}
									</a>
								@endcan

								@can('view_se_buttons', $book)
									<div class="dropdown-divider"></div>

									<a class="dropdown-item text-lowercase" target="_blank"
									   href="{{ \Litlife\Url\Url::fromString('https://yandex.ru/search/')->withQueryParameter('text', $book->title.' '.implode(' ', $book->writers->pluck('name')->toArray() ?? [])) }}">
										{{ __('book.search_in_yandex') }}
									</a>
									<a class="dropdown-item text-lowercase" target="_blank"
									   href="{{ \Litlife\Url\Url::fromString('https://www.google.ru/search')->withQueryParameter('q', $book->title.' '.implode(' ', $book->writers->pluck('name')->toArray() ?? [])) }}">
										{{ __('book.search_in_google') }}
									</a>

								@endcan

								@can('change_sell_settings', $book)
									<a class="dropdown-item text-lowercase"
									   href="{{ route('books.sales.edit', $book) }}">
										{{ __('book.sales') }}
									</a>
								@endcan

								@can('change_access', $book)
									<div class="dropdown-divider"></div>

									<a class="dropdown-item text-lowercase"
									   href="{{ route('books.access.edit', $book) }}">
										{{ __('book.read_download_access') }}
									</a>
									<a class="dropdown-item text-lowercase"
									   href="{{ route('books.close_access', $book) }}">
										{{ __('book.close_read_download_access') }}
									</a>
									<div class="dropdown-divider"></div>
								@endcan

								@can('replaceWithThis', $book)
									<a class="dropdown-item text-lowercase"
									   href="{{ route('books.replace_book_created_by_another_user.form',
									   ['book' =>  $book]) }}">
										{{ __('book.replace_book_created_by_another_user') }}
									</a>
								@endcan

								@can('addKeywords', $book)
									<a class="dropdown-item text-lowercase"
									   href="{{ route('books.keywords.index', $book) }}">
										{{ __('book.edit_keywords') }}
									</a>
								@endcan


								@can ('attachAward', $book)
									<a class="dropdown-item text-lowercase"
									   href="{{ route('books.awards.index', $book) }}">
										{{ trans_choice('award.awards', 2) }}
									</a>
								@endcan

								@can('addToPrivate', $book)

									<a class="dropdown-item text-lowercase"
									   href="{{ route('books.add_to_private', $book) }}">
										{{ __('book.add_to_private') }}
									</a>
								@endcan

								@can('create', App\AdminNote::class)
									<a class="dropdown-item text-lowercase"
									   href="{{ route('admin_notes.create', ['type' => 'book', 'id' => $book->id]) }}">
										{{ __('book.create_admin_note') }}
									</a>
								@endcan

								@can ('watch_activity_logs', $book)
									<a class="dropdown-item text-lowercase"
									   href="{{ route('books.activity_logs', $book) }}">
										{{ __('book.logs') }}
									</a>
								@endcan

								@can ('refresh_counters', $book)
									<a class="dropdown-item text-lowercase"
									   href="{{ route('books.refresh_counters', $book) }}">
										{{ __('common.refresh_counters') }}
									</a>
								@endcan

								@can ('open_comments', $book)
									<a class="dropdown-item text-lowercase"
									   href="{{ route('books.open_comments', $book) }}">
										{{ __('book.open_comments') }}
									</a>
								@elsecan ('close_comments', $book)
									<a class="dropdown-item text-lowercase"
									   href="{{ route('books.close_comments', $book) }}">
										{{ __('book.close_comments') }}
									</a>
								@endcan

								@can ('enableForbidChangesInBook', $book)
									<a class="dropdown-item text-lowercase"
									   href="{{ route('books.forbid_changes.enable', $book) }}">
										{{ __('book.forbid_changes') }}
									</a>
								@elsecan ('disableForbidChangesInBook', $book)
									<a class="dropdown-item text-lowercase"
									   href="{{ route('books.forbid_changes.disable', $book) }}">
										{{ __('book.allow_changes') }}
									</a>
								@endcan

								@can ('createTextProcessing', $book)
									<a class="dropdown-item text-lowercase"
									   href="{{ route('books.text_processings.create', $book) }}">
										{{ __('book.text_processings') }}
									</a>
								@endcan

								<a class="abuse dropdown-item text-lowercase"
								   href="{{ route("complains.report", ['type' => 'book', 'id' => $book->id]) }}">
									{{ __('common.complain') }}
								</a>

									@can ('deletingOnlineReadAndFiles', $book)
										<div class="dropdown-divider"></div>

										<a class="dropdown-item text-lowercase"
										   href="{{ route("books.deleting_online_read_and_files", $book) }}">
											{{ __('book.deleting_reads_and_files') }}
										</a>
									@endcan

							</div>
						</div>
					</div>
				</div>

				@section('grouped_books')
					@if ($book->isInGroup())
						<div>
							<a itemprop="url" class="btn btn-light btn-sm"
							   href="{{ route('books.editions.index', ['book' => $book]) }}">
								{{ __('book.all_book_in_group') }} ({{ intval($book->editions_count) }})
							</a>

							@if ($book->isNotMainInGroup() and (!empty($book->mainBook)))
								<a class="btn btn-light btn-sm mb-0"
								   href="{{ route('books.show', $book->mainBook) }}">
									{{ __('book.exist_better_version') }}
								</a>
							@endif
						</div>
					@endif
				@show
			</div>

			<div class="card-body">

				@section('description')
					@include('book.show.description')
				@show

				<hr/>

				@section('vote')
					<div class="row mb-3">
						<div class="col-12">
							@if ($mainBook->user_vote_count > 0 and $mainBook->vote_average > 0)
								<div itemprop="aggregateRating" class="d-none"
									 itemscope itemtype="http://schema.org/AggregateRating">
									<meta itemprop="worstRating" content="1"/>
									<meta itemprop="bestRating" content="10"/>
									<meta itemprop="ratingCount" content="{{ $mainBook->user_vote_count }}"/>
									<meta itemprop="ratingValue" content="{{ round($mainBook->vote_average, 2) }}"/>
								</div>
							@endif

							<h6>{{ __('book.vote_average') }}
								@if ($mainBook->isRatingChanged())
									<small class="ml-1" data-toggle="tooltip"
										   data-placement="top" title="{{ __('book.rating_chnaged_wait_for_refresh') }}">
										<i class="fas fa-sync-alt"></i>
									</small>
								@endif
							</h6>
							<h3 class="d-inline-block bold mb-0 mr-3">
								<span>{{ $book->getVoteAverageForTable() }}</span>
								<small>/ 10</small>
							</h3>

							<a class="btn btn-light" href="{{ route('books.votes', $book) }}"
							   data-toggle="tooltip" data-placement="top" title="{{ __('book.all_votes') }}">
								<i class="fas fa-users"></i> {{ $mainBook->user_vote_count }}
							</a>

							<a class="btn btn-light"
							   href="{{ route('books.votes', ['book' => $book, 'gender' => 'female']) }}"
							   data-toggle="tooltip" data-placement="top" title="{{ __('book.votes_female') }}"
							   style="text-decoration: none;">
								<i class="fas fa-female mr-1 color-pink"></i> {{ $mainBook->female_vote_count }}
							</a>

							<a class="btn btn-light"
							   href="{{ route('books.votes', ['book' => $book, 'gender' => 'male']) }}"
							   data-toggle="tooltip" data-placement="top" title="{{ __('book.votes_male') }}"
							   style="text-decoration: none;">
								<i class="fas fa-male mr-1 color-blue"></i> {{ $mainBook->male_vote_count }}
							</a>

							<button type="button" class="btn btn-light" data-toggle="modal"
									data-target="#voteStatistic">
								<i class="fas fa-poll-h"></i>
								<span class="d-none d-md-inline">{{ __('Ratings disposition') }}</span>
							</button>

							@push('body_append')

								<div class="modal" id="voteStatistic" tabindex="-1" role="dialog"
									 aria-labelledby="exampleModalLabel"
									 aria-hidden="true">
									<div class="modal-dialog" role="document">
										<div class="modal-content">
											<div class="modal-header">
												<h5 class="modal-title" id="exampleModalLabel">
													{{ __('Ratings disposition') }}
												</h5>
												<button type="button" class="close" data-dismiss="modal"
														aria-label="Close">
													<span aria-hidden="true">&times;</span>
												</button>
											</div>
											<div class="modal-body">

												@if (filled($mainBook->rate_info))
													@foreach (config('litlife.votes') as $vote)
														<div class="d-flex">
															<div class="p-1 flex-grow-1" style="width: 80px">
																<i class="far fa-star"></i> {{ $vote }}
															</div>
															<div class="p-1 w-100">
																<div class="progress" style="height:9px; margin:8px 0;">
																	<div class="progress-bar" role="progressbar"
																		 aria-valuenow="{{ $mainBook->rate_info[$vote]['percent'] }}"
																		 aria-valuemin="0" aria-valuemax="100"
																		 style="width: {{ $book->rate_info[$vote]['percent'] }}%">
																		<span class="sr-only">{{ $book->rate_info[$vote]['percent'] }}%</span>
																	</div>
																</div>
															</div>
															<div class="p-1" style="width: 80px">
																{{ $mainBook->rate_info[$vote]['count'] }}
															</div>
														</div>
													@endforeach
												@endif

											</div>
										</div>
									</div>
								</div>

							@endpush
						</div>
					</div>

					<div class="row mb-3">
						<div class="col-12 mb-1">
							<h6 class="mb-2">
								{{ __('Your book rating') }}:
							</h6>

							<div class="d-block d-md-none">

								@if (isset($user_book_vote) and $user_book_vote->vote > 0)
									<h3 class="inline mb-0">
										<x-book-vote :vote="$user_book_vote"/>
									</h3>

									<button class="btn btn-light btn-sm" data-toggle="modal"
											data-target="#ask_user_to_rate_the_book_modal">
										{{ __('Сhange the rating') }}
									</button>
								@else
									<button class="btn btn-primary" data-toggle="modal"
											data-target="#ask_user_to_rate_the_book_modal">
										<i class="far fa-star"></i> {{ __('Rate the book') }}
									</button>
								@endif

								@if (isset($user_book_vote))
									<a class="btn btn-light btn-sm"
									   href="{{ route('books.votes.delete', $book) }}">{{ __('Delete a rating') }}</a>
								@endif
							</div>

							<div class="btn-toolbar flex-wrap d-none d-md-flex" role="toolbar"
								 aria-label="Toolbar with button groups">
								<div id="rating" class="btn-group mr-2 flex-wrap " role="group"
									 aria-label="First group">
									@foreach (config('litlife.votes') as $vote)
										@if (isset($user_book_vote) and $user_book_vote->vote == $vote)
											<a class="btn btn-info" style="color:#FFF" data-vote="{{ $vote }}"
											   data-toggle="tooltip" data-placement="top"
											   title="{{ __('book.vote_descriptions.'.$vote) }}">
												<i class="fas fa-star"></i> {{ $vote }}
											</a>
										@else
											<a class="btn btn-light"
											   href="{{ route('books.vote', ['book' => $book, 'vote' => $vote]) }}"
											   data-vote="{{ $vote }}"
											   data-toggle="tooltip" data-placement="top"
											   title="{{ __('book.vote_descriptions.'.$vote) }}">
												{{ $vote }}
											</a>
										@endif
									@endforeach
								</div>

								@if (isset($user_book_vote))
									<a class="btn btn-light"
									   href="{{ route('books.votes.delete', $book) }}">{{ __('Delete a rating') }}</a>
								@endif
							</div>
						</div>

						@if (isset($user_book_vote))
							<div class="col-12 mb-2 d-flex align-items-center flex-wrap">

								<div class="text-secondary small mr-2">
									{{ __('book_vote.you_voted_at') }}
									<span id="date_of_rating"><x-time :time="$user_book_vote->user_updated_at"/></span>
								</div>

								<a id="change_the_date_of_rating" class="btn btn-sm btn-light"
								   href="{{ route('books.ratings.date.edit', ['book' => $book]) }}">
									{{ __('Change the date') }}
								</a>
							</div>
						@endif

					</div>

				@show

				@section('buttons')
					<div class="row mb-2">
						<div class="col-12 btn-margin-bottom-1">

							@include('like.item', ['item' => $mainBook,'like' => @$auth_user_like, 'likeable_type' => 'book'])

							@include('user_library_button', ['item' => $book, 'user_library' => @$auth_user_library, 'type' => 'book',
							'count' => $book->added_to_favorites_count])

							<button class="btn btn-outline-secondary share" data-toggle="tooltip"
									data-title="{{ e($book->getShareTitle()) }}"
									data-description="{{ e($book->getShareDescription()) }}"
									data-url="{{ route('books.show', ['book' => $book]) }}"
									data-image="{{ e($book->getShareImage()) }}"
									data-placement="top" title="{{ __('book.share') }}">
								<i class="far fa-share-square"></i> {{ __('common.share') }}
							</button>

						</div>
					</div>

					<div class="d-flex mb-3 flex-wrap">
						<div class="d-flex flex-nowrap align-items-center mr-3">

							<div class="mr-2 text-nowrap d-none d-sm-block">{{ __('Your reading status') }}</div>

							<select class="read-status inline custom-select" style="width:200px;">
								@foreach (\App\Enums\ReadStatus::getValues() as $status)
									<option value="{{ $status }}"
											@if ((isset($user_read_status->status)) && ($user_read_status->status == $status)) selected @endif>
										{{ trans_choice('book.read_status_array.'.$status, 1) }}
									</option>
								@endforeach
							</select>

						</div>

						@if (isset($user_read_status))
							<div class="d-flex align-items-center flex-nowrap">

								<div class="text-secondary small mr-2">
									<span id="date_of_read_status"><x-time :time="$user_read_status->user_updated_at"/></span>
								</div>

								<a id="change_the_date_of_read_status" class="btn btn-sm btn-light"
								   href="{{ route('books.read_status.date.edit', ['book' => $book]) }}">
									{{ __('Change the date') }}
								</a>
							</div>
						@endif
					</div>

				@show

				<hr/>

				@section('read_buttons')

					<div class="row">
						<div class="col-12  mb-3 btn-margin-bottom-1">

							@can ('buy_button', $book)

								@auth
									<a href="{{ route('books.purchase', ['book' => $book]) }}"
									   class="btn btn-primary font-weight-bold">
										{{ trans_choice('book.buy_a_book', $book->price, ['price' => $book->price]) }}
									</a>
								@endauth

								@guest
									<button type="button" class="btn btn-primary font-weight-bold"
											data-container="body"
											data-toggle="popover" data-placement="top" data-html="true"
											data-content="{{ __('user.unauthenticated_error_description') }}">
										{{ trans_choice('book.buy_a_book', $book->price, ['price' => $book->price]) }}
									</button>
								@endguest

							@endcan

							@can ('view_read_button', $book)

								<a class="btn btn-primary font-weight-bold" data-button="contune-reading"
								   data-toggle="tooltip" data-placement="top"
								   style="{{ !$book->remembered_pages->first() ? 'display:none;' : '' }}"
								   title="{{ isset($remembered_page) ? __('book.continue_reading_tooltip', ['page' => $remembered_page->page]) : '' }}"
								   href="{{ route('books.read.online', $book) }}">
									{{ __('book.continue_reading') }}
								</a>

								<button class="btn btn-outline-primary "
										style="{{ isset($remembered_page) ? '' : 'display:none;' }}"
										data-toggle="tooltip" data-placement="top"
										title="{{ __('book.stop_reading') }}"
										data-button="stop-reading">
									<i class="fas fa-stop-circle"></i>
								</button>

								<a href="{{ route('books.read.online', $book) }}"
								   data-button="reading" class="btn btn-primary font-weight-bold"
								   style="{{ isset($remembered_page) ? 'display:none;' : '' }}">
									{{ __('common.read_online') }}
								</a>

							@endcan

							@can ('view_download_files', $book)

								@if (!empty($book->files) and $book->files->count())
									<ul id="files" class="files list-group list-group-flush mt-3">
										@foreach ($book->files as $file)
											@include('book.show.file_item', ['item' => $file])
										@endforeach
									</ul>
								@endif

							@endcan

							@can ('addFiles', $book)

								<a class="btn btn-light mt-1" href="{{ route('books.files.create', compact('book')) }}">
									<i class="fas fa-plus"></i> {{ __('book_file.attach_one_more_file') }}
								</a>

							@endcan

							@if (!$book->isReadOrDownloadAccess())

							<!--noindex-->
								<div class="alert alert-warning mt-3" role="alert">
									<p class="no_access_text"></p>
									@can ('change_access', $book)
										@if ($book->secret_hide_reason)
											<p>{{ __('book.reason_for_changing_access') }}: {{ $book->secret_hide_reason }}</p>
										@endif
									@endcan
								</div>
								<!--/noindex-->

								@push('body_append')
									<script type="text/javascript">
										$(function () {
											var s = '{{ __('book.message_that_there_is_no_access') }}';
											$('.no_access_text').html(s);
										});
									</script>
								@endpush
							@endcan

						</div>
					</div>

				@show

				@section('admin_note')
					@include('admin_note.item', ['object' => $book, 'type' => 'book'])
				@show

				@section('annotation')
					@if (is_object($book->annotation) and !empty($content = $book->annotation->getContent()))
						<div class="row">
							<div class="col-12">
								<div id="annotation" itemprop="description" class="book_text"
									 style="max-height: 300px; overflow-y:hidden;">
									{!! $content !!}
								</div>
								<div class="btn btn-light expand-biography"
									 style="display: none">{{ __('common.expand') }}</div>
								<div class="btn btn-light compress-biography"
									 style="display: none">{{ __('common.compress') }}</div>
							</div>
						</div>
					@endif

				@show

				@section('keywords')
					<div class="row my-3">
						<div class="col-12 keywords btn-margin-bottom-1">

							@if (is_object($book->originBookKeywords) and $book->originBookKeywords->count())

								<meta itemprop="keywords"
									  content="{{ implode(', ', $book->originBookKeywords->pluck('keyword.text')->toArray()) }}"/>

								@foreach ($book->originBookKeywords as $book_keyword)
									@if (isset($book_keyword->keyword))
										<button class="keyword button btn btn-sm btn-outline-secondary"
												data-id="{{ $book_keyword->id }}"
												data-book-id="{{ $book_keyword->book_id }}"
												data-target="book_keyword_{{ $book_keyword->id }}">
											<h4 class="h6 font-weight-normal d-inline">{{ $book_keyword->keyword->text }}</h4>
										</button>

										<div id="book_keyword_{{ $book_keyword->id }}" style="display: none">
											<div>
												@can ('vote', $book_keyword)
													<div class="btn-group text-center mb-2" role="group">
														<a class="up btn btn-sm @if (isset($book_keyword->user_vote->vote) && $book_keyword->user_vote->vote > 0)
																active btn-success @else btn-light @endif"
														   type="button">
															<span class="far fa-thumbs-up"></span> {{ __('book_keyword.match') }}
														</a>
														<a class="down btn btn-sm @if (isset($book_keyword->user_vote->vote) && $book_keyword->user_vote->vote < 0)
																active btn-danger @else btn-light @endif" type="button">
															<span class="far fa-thumbs-down"></span> {{ __('book_keyword.didnt_match') }}
														</a>
													</div>
												@endcan
												<div class="text-center">
													<a class="btn btn-light"
													   href="{{ route('books', ['kw' => $book_keyword->keyword->text]) }}">
														{{ __('book.all_books') }}
													</a>
												</div>
											</div>
										</div>
									@endif
								@endforeach

							@endif

							@can('addKeywords', $book)
								<a href="{{ route('books.keywords.index', $book) }}" class="btn btn-sm btn-primary">
									{{ __('book_keyword.add') }}
								</a>
							@endcan
						</div>
					</div>

				@show

			</div>
		</div>

		@section('comments')
			@isset($top_comments)
				@if (count($top_comments) > 0)
					<div class="top_comments">
						@foreach ($top_comments as $comment)
							@include("comment.list.default", ['book' => $book, 'item' => $comment])
						@endforeach
					</div>
				@endif
			@endisset

			@can('commentOn', $book)
				@include('comment.create_form', ['commentable_type' => 'book', 'commentable_id' => $book->id, 'canLeaveCommentInPersonalAccess' => true])
			@endcan

			<div class="comments">
				@include('book.show.comments', ['comments' => $comments])
			</div>
		@show
	</div>

</div>

@include('book.age_access_modal')

@endsection
