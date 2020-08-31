@extends('layouts.app')

@push('scripts')
	<script src="{{ mix('js/authors.show.js', config('litlife.assets_path')) }}"></script>
@endpush

@push ('css')

@endpush

@section('content')

	@if ($author->isSentForReview())

		<p class="alert alert-info">{{ __('author.on_review_please_wait') }}</p>

	@endif

	@if (session('success'))
		<div class="alert alert-success alert-dismissable">
			{{ session('success') }}
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		</div>
	@endif


	<div class="author" itemscope itemtype="http://schema.org/Person">

		<div class="row">
			<div class="col-12">

				@if (($author->isMerged()) and (!empty($author->redirect_to_author)))

					<div class="alert alert-danger">
						{{ __('author.merged') }}.
						<a href="{{ route('authors.show', $author->redirect_to_author) }}" class="alert-link">
							{{ __('author.go_to_merged_author') }}
						</a>
					</div>

				@endif

			</div>
		</div>


		<div class="row">

			<div class="col-md-5 col-lg-4 col-xl-3 text-center">

				<div class="card  mb-3">
					<div class="card-body">

						<x-author-photo :author="$author" width="200" height="400"
										class="img-fluid rounded pointer lazyload"
										href="{{ route('authors.photo', $author) }}"
										style="max-width: 100%;"/>

					</div>
				</div>
			</div>

			<div class="col-md-7 col-lg-8 col-xl-9">
				<div class="card  mb-3">
					<div class="card-header">
						<div class="d-flex w-100 justify-content-between">
							<h2 class="inline break-word h5" itemprop="name">
								<x-author-name :author="$author" href="0"/>
							</h2>

							<div class="ml-auto">
								<div class="btn-group" data-toggle="tooltip" data-placement="top"
									 title="{{ __('common.open_actions') }}">
									<button class="btn btn-light dropdown-toggle" type="button"
											id="author_{{ $author->id }}"
											data-toggle="dropdown"
											aria-haspopup="true"
											aria-expanded="false">
										<i class="fas fa-ellipsis-h"></i>
									</button>

									<div class="dropdown-menu dropdown-menu-right"
										 aria-labelledby="author_{{ $author->id }}">

										@can ('update', $author)
											<a class="dropdown-item text-lowercase"
											   href="{{ route('authors.edit', $author) }}">
												{{ __('common.edit') }}
											</a>
										@endcan

										@can ('makeAccepted', $author)
											<a class="dropdown-item text-lowercase"
											   href="{{ route('authors.make_accepted', $author) }}">
												{{ __('author.make_accepted') }}
											</a>
										@endcan

										@can ('sales_request', $author)
											<a class="dropdown-item text-lowercase"
											   href="{{ route('authors.sales.request', $author) }}">
												{{ __('author.sell_request') }}
											</a>
										@endcan

										@can ('viewManagers', $author)
											<a class="dropdown-item text-lowercase"
											   href="{{ route('authors.managers', $author) }}">
												{{ __('author.authors_and_editors') }}
											</a>
										@endcan

										@can ('group', $author)
											<a class="dropdown-item text-lowercase"
											   href="{{ route('authors.group.index', $author) }}">
												{{ __('author.group_page') }}
											</a>
										@endcan

										@can ('delete', $author)
											<a class="dropdown-item text-lowercase"
											   href="{{ route('authors.delete', $author) }}">
												{{ __('common.delete') }}
											</a>
										@endcan

										@can ('delete', $managers->where('user_id', auth()->id())->first())
											<a class="dropdown-item text-lowercase"
											   href="{{ route('managers.destroy', $managers->where('user_id', auth()->id())->first()) }}">
												{{ __('author.stop_edit') }}
											</a>
										@endcan

										@can ('restore', $author)
											<a class="dropdown-item text-lowercase"
											   href="{{ route('authors.delete', $author) }}">
												{{ __('common.restore') }}
											</a>
										@endcan

										@can('create', App\AdminNote::class)
											<a class="dropdown-item text-lowercase"
											   href="{{ route('admin_notes.create', ['type' => 'author', 'id' => $author->id]) }}">
												{{ __('author.create_admin_note') }}
											</a>
										@endcan

										@can ('watch_activity_logs', $author)
											<a class="dropdown-item text-lowercase"
											   href="{{route('authors.activity_logs', $author) }}">
												{{ __('author.logs') }}
											</a>
										@endcan

										@can ('refresh_counters', $author)
											<a class="dropdown-item text-lowercase"
											   href="{{ route('authors.refresh_counters', $author) }}">
												{{ __('author.refresh_counters') }}
											</a>
										@endcan

										@can ('booksCloseAccess', $author)
											<a class="dropdown-item text-lowercase"
											   href="{{ route('authors.books.close_access', $author) }}">
												{{ __('author.close_access_to_all_books') }}
											</a>
										@endcan

										@can ('editorRequest', $author)
											<a class="dropdown-item text-lowercase"
											   href="{{ route('authors.editor.request', ['author' => $author]) }}">
												{{ __('author.i_want_to_be_an_editor') }}
											</a>
										@endcan
									</div>
								</div>
							</div>
						</div>

						@if (!$author->trashed() and !empty($author->originalFullName))
							<h3 class="text-muted h6">
								{{ $author->originalFullName }}
							</h3>
						@endif
					</div>
					<div class="card-body">
						@if ((!$author->trashed()) and ($author->isAccepted()))
							@include('admin_note.item', ['object' => $author, 'type' => 'author'])
						@endif

						@if (!$author->trashed())

							@if ((!empty($author->group->authors)) and ($author->group->authors->count()))

								<div class="row mb-3">
									<div class="col-12">
										{{ __('author.other_pages') }}:

										@if (isset($author->group->authors))
											@foreach ($author->group->authors as $alias_author)
												<x-author-name :author="$alias_author"/>{{ $loop->last ? '' : ', ' }}
											@endforeach
										@endif
									</div>
								</div>

							@endif

						@endif

						<div class="row mb-2">
							<div class="col-12 btn-margin-bottom-1">

								@if ((!empty($author_user)) and ($author_user->id != auth()->id()))
									<a class="btn btn-light"
									   href="{{ route('users.messages.index', ['user' => $author_user]) }}">
										{{ __('author.write_to_author') }}
									</a>
								@endif

								@include('like.item', ['item' => $author, 'like' => pos($author->likes) ?: null, 'likeable_type' => 'author'])

								@include('user_library_button', ['item' => $author, 'user_library' => pos($author->library_users) ?: null, 'type' =>
								'author', 'id' => $author->id, 'count' => $author->added_to_favorites_count,
								'tooltip_pressed' => __('author.add_to_favorites_to_receive_notifications_about_new_books_by_the_author')])

								<select class="read-status inline custom-select mb-1" style="width:200px;">
									@foreach (\App\Enums\ReadStatus::getValues() as $status)
										<option value="{{ $status }}"
												@if ((isset($user_read_status->status)) && ($user_read_status->status == $status)) selected @endif>
											{{ trans_choice('author.read_status_array.'.$status, 1) }}
										</option>
									@endforeach
								</select>

								<a class="btn btn-light"
								   href="{{ route('authors.books.files.urls', $author) }}"
								   data-toggle="tooltip" data-placement="top"
								   title="{{ __('author.download_books_file_links') }}">
									<i class="fas fa-download"></i>
								</a>


								<button class="btn btn-outline-secondary share" data-toggle="tooltip"
										data-title="{{ e($author->getShareTitle()) }}"
										data-description="{{ e($author->getShareDescription()) }}"
										data-url="{{ route('authors.show', ['author' => $author]) }}"
										data-image="{{ e($author->getShareImage()) }}"
										data-placement="top" title="{{ __('author.share') }}">
									<i class="far fa-share-square"></i> {{ __('common.share') }}
								</button>

							</div>
						</div>

						@if (!$author->trashed())
							<div class="row mb-3">
								<div class="col-12">
									@if ($manager = $managers->where('user_id', auth()->id())->first())
										@if ($manager->isSentForReview())

											@if ($manager->isAuthorCharacter())
												<div class="alert alert-warning" role="alert">
													{{ __('manager.request_on_review') }}
													<a class="btn btn-sm btn-light"
													   href="{{ route('managers.destroy', $manager) }}">{{ __('common.delete') }}</a>
												</div>
											@elseif ($manager->isEditorCharacter())
												<div class="alert alert-warning" role="alert">
													{{ __('manager.request_on_review') }}
													<a class="btn btn-sm btn-light"
													   href="{{ route('managers.destroy', $manager) }}">{{ __('common.delete') }}</a>
												</div>
											@endif
										@endif
									@else
										@can ('verficationRequest', $author)
											<a class="btn btn-light"
											   href="{{ route('authors.verification.request', ['author' => $author]) }}">
												{{ __('author.iam_the_author') }}
											</a>
										@endcan
									@endif

									@if (!$author->isPrivate())

										@can('create', \App\AuthorRepeat::class)
											<a class="btn btn-light" target="_blank"
											   href="{{ route('author_repeats.create', ['ids' => $author->id]) }}">
												{{ __('author_repeat.report_about_repeat') }}
											</a>
										@endcan
									@endif
								</div>
							</div>
						@endif

						@if ($managers->count())
							@if ($authors = $managers->where('status', \App\Enums\StatusEnum::Accepted)->where('character', 'author') and $authors->count() > 0)
								<div class="">
									<span class="font-weight-bold small">{{ trans_choice('author.authors', 1) }}:</span>
									@foreach ($authors as $manager)
										<x-user-name :user="$manager->user"/>{{ $loop->last ? '' : ', ' }}
									@endforeach
								</div>
							@endif

							@if ($editors = $managers->where('status', \App\Enums\StatusEnum::Accepted)->where('character', 'editor') and $editors->count() > 0)
								<div class="">
									<span class="font-weight-bold small">{{ trans_choice('author.editors', $editors->count()) }}:</span>
									@foreach ($editors as $manager)
										<x-user-name :user="$manager->user"/>{{ $loop->last ? '' : ', ' }}
									@endforeach
								</div>
							@endif
						@endif


						<div class="row">
							<div class="col-md-6">
								<div class="row">

									@if (!$author->trashed())

										@if ($author->added_to_favorites_count > 0)
											<div class="col-12 text-wrap">
											<span class="font-weight-bold small">
												{{ trans_choice('author.added_to_favorites_times', $author->added_to_favorites_count, ['count' => $author->added_to_favorites_count]) }}
											</span>
											</div>
										@endif

										@if (!$author->isPrivate())
											<div class="col-12 text-wrap">
												<span class="font-weight-bold small">{{ __('author.vote_average') }}:</span>
												<span>{{ round($author->vote_average, 2) }}</span>
												(<span>{{ $author->votes_count }}</span>)
											</div>
										@endif

										<div class="col-12 text-wrap">
											<span class="font-weight-bold small">{{ __('author.gender') }}:</span> {{ __('gender.'.$author->gender) }}
											<meta itemprop="gender" content="{{ $author->gender }}"/>
										</div>

										@if (!empty($author->language))
											<div class="col-12 text-wrap">
												<span class="font-weight-bold small">{{ __('author.lang') }}:</span> {{ $author->language->name }}
											</div>
										@endif


										@if (!empty($author->home_page))
											<div class="col-12 text-wrap">
												<span class="font-weight-bold small">{{ __('author.home_page') }}:</span>
												<a target="_blank" itemprop="url" href="{{ route('away', ['url' => $author->home_page]) }}">
													{{ $author->home_page }}
												</a>
											</div>
										@endif

										@if (!empty($author->wikipedia_url))
											<div class="col-12 text-truncate">
												<span class="font-weight-bold small">{{ __('author.wikipedia_url') }}:</span>
												<a target="_blank"
												   href="{{ route('away', ['url' => $author->wikipedia_url]) }}">{{ $author->wikipedia_url }}</a>
											</div>
										@endif

										@if (isset($author->born_date))
											<div class="col-12 text-wrap">
												<span class="font-weight-bold small">{{ __('author.born_date') }}:</span>
												<span itemprop="birthDate">{{ $author->born_date }}</span>
											</div>
										@endif

										@if (!empty($author->born_place))
											<div class="col-12 text-wrap">
												<span class="font-weight-bold small">{{ __('author.born_place') }}:</span>
												<span itemprop="birthPlace" itemscope
													  itemtype="http://schema.org/Place">
                                            <span itemprop="name">{{ $author->born_place }}</span>
                                        </span>
											</div>
										@endif

										@if (isset($author->dead_date))
											<div class="col-12 text-wrap">
												<span class="font-weight-bold small">{{ __('author.dead_date') }}:</span>
												<span>{{ $author->dead_date }}</span>
											</div>
										@endif

										@if (!empty($author->dead_place))
											<div class="col-12 text-wrap">
												<span class="font-weight-bold small">{{ __('author.dead_place') }}:</span>
												<span itemprop="name">{{ $author->dead_place }}</span>
											</div>
										@endif

										@can ('display_technical_information', \App\Author::class)
											@if (isset($author->editUser))
												<div class="col-12 text-wrap">
													<span class="font-weight-bold small">{{ trans_choice('user.edited', $author->editUser->gender) }}:</span>
													<x-user-name :user="$author->editUser"/>
													<x-time :time="$author->user_edited_at"/>
												</div>
											@endif
										@endcan
									@endif

									<div class="col-12 text-wrap">
										<span class="font-weight-bold small">{{ __('author.id') }}:</span>
										<span itemprop="identifier">{{ $author->id }}</span>
									</div>

									@if ($manager = $managers->where('character', 'author')->where('user_id', auth()->id())->where('can_sale', true)->first())
										<div class="col-12 text-wrap">
											<span class="font-weight-bold small">{{ __('manager.profit_percent') }}:</span>
											<span>{{ $manager->profit_percent }}%</span>
										</div>
									@endif

								</div>
							</div>

							<div class="col-md-6">
								@if (!$author->trashed() and !$author->isPrivate())
									<span class="font-weight-bold small">{{ __('author.books_views') }}</span><br/>
									<span class="font-weight-bold small">{{ __('author.view_day') }}:</span> {{ $author->view_day }}
									<br/>
									<span class="font-weight-bold small">{{ __('author.view_week') }}:</span> {{ $author->view_week }}
									<br/>
									<span class="font-weight-bold small">{{ __('author.view_month') }}:</span> {{ $author->view_month }}
									<br/>
									<span class="font-weight-bold small">{{ __('author.view_year') }}:</span> {{ $author->view_year }}
									<br/>
									<span class="font-weight-bold small">{{ __('author.view_all') }}:</span> {{ $author->view_all }}
									<br/>
								@endif
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		@if (!empty($author->biography))
			<div class="card mb-3">
				<div class="card-body">
					@if (!$author->trashed() and isset($author->biography))
						<div id="biography" style="max-height: 150px; overflow-y:hidden;">
							{!! $author->biography->text !!}
						</div>

						<button class="btn btn-secondary btn-sm expand-biography" style="display: none"><i
									class="fas fa-expand"></i>
							&nbsp; {{ __('common.expand') }}</button>

						<button class="btn btn-secondary btn-sm compress-biography" style="display: none"><i
									class="fas fa-compress"></i>
							&nbsp; {{ __('common.compress') }}</button>
					@endif
				</div>
			</div>
		@endif

		@if (!$author->trashed())

			<div data-author_id="{{ $author->id }}">

				<div class="card">

					<div class="card-header">
						<ul class="nav nav-tabs card-header-tabs" id="author_tab">

							<li class="nav-item">
								<a class="nav-link {{ isActiveRoute('authors.show') }}" href="#books" data-toggle="tab">
									{{ trans_choice('book.books', 2) }}
									<span class="badge badge-light">{{ $books_count }}</span>
								</a>
							</li>

							<li class="nav-item">
								<a class="nav-link" href="#comments" data-toggle="tab">
									{{ __('author.books_comments') }} <span
											class="badge badge-light">{{ $author->comments_count }}</span>
								</a>
							</li>

							<li class="nav-item">
								<a class="nav-link" href="#votes" data-toggle="tab">
									{{ __('author.books_votes') }}
									<span class="badge badge-light">{{ $author->votes_count }}</span>
								</a>
							</li>

							@if ($author->isAccepted())
								<li class="nav-item">
									<a class="nav-link" href="#forum" data-toggle="tab">
										{{ __('author.forum') }} <span class="badge badge-light">@if (isset($author->forum))
												{{ $author->forum->post_count }}
											@else
												0
											@endif
                                    </span>
									</a>
								</li>
							@endif

						</ul>
					</div>
					<div class="card-body p-2">
						<div class="tab-content">
							<div class="tab-pane {{ isActiveRoute('authors.show') }}" id="books">
								@include('author.books')

								{{-- <div class="table-responsive">

								   <table class="table table-striped">

									   <thead>
									   <tr>
										   <td>
											   Название книги
										   </td>
										   <td>
											   Оценка
										   </td>
										   <td>
											   Количество оценок
										   </td>
										   <td>
											   Количество комм
										   </td>
										   <td>
											   Статус
										   </td>
										   <td>
											   Добавлена
										   </td>

									   </tr>
									   </thead>

									   @foreach ($author->books as $book)

										   <tr>
											   <td><a href="{{ route('books.show', $book) }}">{{ $book->title }}</a>

											   </td>
											   <td>{{ round($book->new_vote_average, 2) }}

											   </td>
											   <td>{{ $book->user_vote_count }}</td>
											   <td>{{ $book->comment_count }}</td>
											   <td></td>
											   <td>{{ $book->created_at->diffForHumans() }}</td>
										   </tr>

									   @endforeach

								   </table>

							   </div>
		   --}}
							</div>
							<div class="tab-pane" id="comments">

							</div>
							@if ($author->isAccepted())
								<div class="tab-pane" id="forum">

								</div>
							@endif
							<div class="tab-pane" id="votes">

							</div>
						</div>
					</div>
				</div>
			</div>

		@endif
	</div>



@endsection
