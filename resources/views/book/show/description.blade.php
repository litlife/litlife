<div class="row mb-3" itemprop="description">
	<div class="col-lg-6">

		@if ((isset($book->writers)) and ($book->writers->count()))
			<div>
				<span class="font-weight-bold small">{{ trans_choice('author.writers', $book->writers->count()) }}:</span>
				<span class="font-weight-bold">
                @foreach ($book->writers as $author)
						<h3 itemprop="author" class="h6 d-inline"><x-author-name :author="$author" itemprop="name"/></h3>{{ $loop->last ? '' : ', ' }}
					@endforeach
            </span>
			</div>
		@endif

		@if ((isset($book->genres)) and ($book->genres->count()))
			<div>
				<span class="font-weight-bold small">{{ trans_choice('genre.genres', $book->genres->count()) }}:</span>

				@foreach ($book->genres as $genre)
					<a href="{{ route('genres.show', ['genre' => $genre->getIdWithSlug()]) }}" itemprop="genre">
						<h4 class="h6 d-inline font-weight-normal">{{ $genre->name }}</h4></a>{{ $loop->last ? '' : ', ' }}
				@endforeach
			</div>
		@endif

		@if ((isset($book->sequences)) and ($book->sequences->count()))
			<div>
				<span class="font-weight-bold small">{{ trans_choice('book.sequences', $book->sequences->count()) }}:</span>

				@foreach ($book->sequences as $sequence)
					<h4 class="h6 d-inline font-weight-normal">@include('sequence.name', $sequence)</h4>{{ $sequence->pivot->number ? ' #'.$sequence->pivot->number : ''}}{{ $loop->last ? '' : ', ' }}
				@endforeach
			</div>
		@endif

		@if ((isset($mainBook->awards)) and ($mainBook->awards->count()))
			<div>
				<span class="font-weight-bold small">{{ trans_choice('award.awards', $mainBook->awards->count()) }}:</span>

				@foreach ($mainBook->awards as $award)
					<h4 class="h6 d-inline font-weight-normal">{{ $award->award->title }}</h4>{{ empty($award->year) ? '' : ' - '.$award->year.' '.__('common.year') }}{{ $loop->last ? '' : ', ' }}
				@endforeach
			</div>
		@endif

		@if (!empty($book->page_count))
			<div>
				<span class="font-weight-bold small">{{ __('book.page_count') }}:</span>
				<span itemprop="numberOfPages">{{ $book->page_count }}</span>
			</div>
		@endif

		@if (!empty($book->characters_count))
			<div>
				<span class="font-weight-bold small">{{ __('book.characters_count') }}:</span>
				{{ $book->characters_count }}
			</div>
		@endif

		@if ($mainBook->isAccepted())

			@if ($book->added_to_favorites_count > 0)
				<div>
					<span class="font-weight-bold small">
						{{ trans_choice('book.added_to_favorites_times', $book->added_to_favorites_count, ['count' => $book->added_to_favorites_count]) }}
					</span>
				</div>
			@endif

			@if (!empty($mainBook->user_read_count))
				<div>
					<span class="font-weight-bold small">{{ trans_choice('user.read_status_array.readed', $mainBook->user_read_count) }}:</span>
					<a href="{{ route('books.readed', $mainBook) }}">{{ $mainBook->user_read_count }}</a>
				</div>
			@endif

			@if (!empty($mainBook->user_read_later_count))
				<div>
					<span class="font-weight-bold small">{{ trans_choice('user.read_status_array.read_later', $mainBook->user_read_later_count) }}:</span>
					<a href="{{ route('books.read_later', $mainBook) }}">{{ $mainBook->user_read_later_count }}</a>
				</div>
			@endif

			@if (!empty($mainBook->user_read_now_count))
				<div>
					<span class="font-weight-bold small">{{ trans_choice('user.read_status_array.read_now', $mainBook->user_read_now_count) }}:</span>
					<a href="{{ route('books.read_now', $mainBook) }}">{{ $mainBook->user_read_now_count }}</a>
				</div>
			@endif

			@if (!empty($mainBook->user_read_not_complete_count))
				<div>
                    <span class="font-weight-bold small">{{ trans_choice('user.read_status_array.read_not_complete', $mainBook->user_read_not_complete_count) }}
                        :</span>
					<a href="{{ route('books.read_not_complete', $mainBook) }}">{{ $mainBook->user_read_not_complete_count }}</a>
				</div>
			@endif
		@endif

		<div>
			<span class="font-weight-bold small">ID:</span> <span itemprop="identifier">{{ $book->id }}</span>
		</div>
		@if (!empty($book->language))
			<div>
				<span class="font-weight-bold small">{{ __('book.ti_lb') }}:</span>
				<span itemprop="inLanguage" content="{{ $book->language->code }}">{{ $book->language->name }}</span>
			</div>
		@endif
		@if (!empty($book->originalLang))
			<div>
				<span class="font-weight-bold small">{{ __('book.ti_olb') }}:</span>
				<span>{{ $book->originalLang->name }}</span>
			</div>
		@endif

		@if (!empty($book->ready_status))
			<div class="text-truncate">
				@switch ($book->ready_status)
					@case ('complete')
					<div class="badge badge-success text-wrap d-inline-block">
						{{ __('book.'.$book->ready_status) }}
						@if ($book->isPostedFreeFragment())
							({{ __('book.free_published_fragment')}})
						@endif
					</div>
					@break
					@case ('complete_but_publish_only_part')
					<span class="badge badge-info text-wrap">{{ __('book.'.$book->ready_status) }}</span>
					@break
					@case ('not_complete_but_still_writing')
					<span class="badge badge-info text-wrap">{{ __('book.'.$book->ready_status) }}</span>
					@break
					@case ('not_complete_and_not_will_be')
					<span class="badge badge-info text-wrap">{{ __('book.'.$book->ready_status) }}</span>
					@break

				@endswitch
			</div>
		@endif

		@include('book.price')

		@if (!empty($book->bought_times_count))
			<div>
				<a href="{{ route('books.users.bought', $book) }}">
					<span class="font-weight-bold small">{{ __('book.bought_times_count') }}:</span>
					<span>{{ $book->bought_times_count }}</span></a>
				@auth
					@if ($purchase = $book->purchases->where('buyer_user_id', auth()->user()->id)->first())
						<span>{{ __('book.you_purchase_this_book') }}</span>
					@endcan
				@endauth
			</div>
		@endif

			<div class="mt-2">
				@if (!empty($collectionsCount))

					<a href="{{ route('books.collections.index', $book) }}">
						<span class="font-weight-bold small">{{ __('Found in collections') }}:</span>
						<span>{{ $collectionsCount }}</span></a>

				@endif

				@can('addToCollection', $book)
				<a class="btn btn-sm btn-outline-primary" href="{{ route('books.collections.create', $book) }}">
					{{ __('Add to the collection') }}
				</a>
				@endcan
			</div>
	</div>
	<div class="col-lg-6">

		@if (!empty($book->year_writing))
			<div>
				<span class="font-weight-bold small">{{ __('book.year_writing') }}:</span> {{ $book->year_writing }}
			</div>
		@endif

		@if ((!empty($book->translators)) and ($book->translators->count()))

			<div>
				<span class="font-weight-bold small">{{ trans_choice('book.translators', $book->translators->count()) }}:</span>

				@foreach ($book->translators as $author)
					<span itemprop="translator"><x-author-name :author="$author"/></span>{{ $loop->last ? '' : ', ' }}
				@endforeach
			</div>

		@endif

		@if ($book->editors->count())
			<div>
				<span class="font-weight-bold small">{{ trans_choice('book.editors', $book->editors->count()) }}:</span>

				@foreach ($book->editors as $author)
					<span itemprop="editor"><x-author-name :author="$author"/></span>{{ $loop->last ? '' : ', ' }}
				@endforeach
			</div>
		@endif

		@if ($book->compilers->count())
			<div>
				<span class="font-weight-bold small">{{ trans_choice('book.compilers', $book->compilers->count()) }}:</span>

				@foreach ($book->compilers as $author)
					<x-author-name :author="$author"/>{{ $loop->last ? '' : ', ' }}
				@endforeach
			</div>
		@endif

		@if (!empty($book->images_exists))
			<div>
				{{ __('book.images_exists') }}
			</div>
		@endif

		@if ($book->illustrators->count())
			<div>
				<span class="font-weight-bold small">{{ trans_choice('book.illustrators', $book->illustrators->count()) }}:</span>

				@foreach ($book->illustrators as $author)
					<span itemprop="illustrator"><x-author-name :author="$author"/></span>{{ $loop->last ? '' : ', ' }}
				@endforeach
			</div>
		@endif

		@if (!empty($book->pi_year))
			<div>
				<span class="font-weight-bold small">{{ __('book.pi_year') }}:</span> {{ $book->pi_year }}
			</div>
		@endif

		@if (!empty($book->pi_pub))
			<div>
				<span class="font-weight-bold small">{{ __('book.pi_pub') }}:</span> {{ $book->pi_pub }}
			</div>
		@endif

		@if (!empty($book->pi_city))
			<div>
				<span class="font-weight-bold small">{{ __('book.pi_city') }}:</span> {{ $book->pi_city }}
			</div>
		@endif

		@can ('display_technical_information', \App\Book::class)

			@if (!empty($book->pi_isbn))
				<div>
					<span class="font-weight-bold small">ISBN:</span> {{ $book->pi_isbn }}
				</div>
			@endif

			@if (!empty($book->rightholder))
				<div>
					<span class="font-weight-bold small">{{ __('book.rightholder') }}:</span> {{ $book->rightholder }}
				</div>
			@endif

			@if (!empty($book->created_at))
				<div>
					@if (!empty($book->create_user))
						<span class="font-weight-bold small">{{ trans_choice('book.created_with_user_gender', $book->create_user->gender) }}</span>

						<x-user-name :user="$book->create_user"/>
					@else
						<span class="font-weight-bold small">{{ trans_choice('book.created_with_user_gender', 'null') }}</span>
					@endif

					<x-time :time="$book->created_at"/>
				</div>
			@endif

			@if (!empty($book->user_edited_at))
				<div>
					@if (!empty($book->edit_user))
						<span class="font-weight-bold small">{{ trans_choice('book.updated_with_user_gender', $book->edit_user->gender) }}</span>
						<x-user-name :user="$book->edit_user"/>
					@else
						<span class="font-weight-bold small">{{ trans_choice('book.updated_with_user_gender', 'null') }}</span>
					@endif

					<x-time :time="$book->user_edited_at"/>
				</div>
			@endif

			@if ($book->isAccepted())
				<div>
					@if (!empty($book->status_changed_user))
						<span class="font-weight-bold small">{{ trans_choice('book.checked', $book->status_changed_user->gender) }}</span>
						<x-user-name :user="$book->status_changed_user"/>
					@else
						<span class="font-weight-bold small">{{ trans_choice('book.checked', 'null') }}</span>
					@endif

					<x-time :time="$book->status_changed_at"/>
				</div>
			@endif

			@if (!empty($book->connected_at))
				<div>
					@if (!empty($book->connect_user))
						<span class="font-weight-bold small">{{ trans_choice('book.connected', $book->connect_user->gender) }}</span>
						<x-user-name :user="$book->connect_user"/>
					@else
						<span class="font-weight-bold small">{{ trans_choice('book.connected', 'null') }}</span>
					@endif

					<x-time :time="$book->connected_at"/>
				</div>
			@endif

			@if ($book->forbid_to_change)
				<div class="text-danger">{{ __('book.forbid_changes_enabled') }}</div>
			@endif

		@else

			@if (!empty($book->created_at))
				<div>
					<span class="font-weight-bold small">{{ trans_choice('book.created_with_user_gender', 'null') }}</span>
					<x-time :time="$book->created_at"/>
				</div>
			@endif

			@if (!empty($book->user_edited_at))
				<div>
					<span class="font-weight-bold small">{{ trans_choice('book.updated_with_user_gender', 'null') }}</span>
					<x-time :time="$book->user_edited_at"/>
				</div>
			@endif

			@if ($book->isAccepted())
				<div>
					<span class="font-weight-bold small">{{ trans_choice('book.checked', 'null') }}</span>
					<x-time :time="$book->status_changed_at"/>
				</div>
			@endif
		@endcan

		@if ($book->trashed() and !empty($log = $book->activities()->where('description', 'deleted')->latest()->first()))
			<div>
				<span class="font-weight-bold small">{{ __('book.deleted_by_user') }}</span>
				<x-user-name :user="$log->causer"/>
				<x-time :time="$log->created_at"/>
			</div>
		@endif

		@if (!empty($book->swear))
			@switch ($book->swear)
				@case ('yes')
				<span class="font-weight-bold small">{{ __('book.swear') }}:</span>
				<span class="text-danger">{{ __('book.swear_array.'.$book->swear) }}</span>
				@break
				@case ('no')
				<span class="font-weight-bold small">{{ __('book.swear') }}:</span>
				<span>{{ __('book.swear_array.'.$book->swear) }}</span>
				@break
			@endswitch
		@endif

		@if (empty($book->isReadAccess()))
			<div class="text-danger">{{ __('book.read_access_disabled') }}</div>
		@endif

		@if (empty($book->isDownloadAccess()))
			<div class="text-danger">{{ __('book.download_access_disabled') }}</div>
		@endif

		<div class="mt-2">
			<a href="{{ route('topics.show', ['topic' => '222']) }}" target="_" class="btn btn-sm btn-outline-primary"
			   data-toggle="tooltip" data-placement="top"
			   title="Нажмите сюда, если нашли ошибку в описании книги или хотите дополнить информацию.">
				Предложить исправление
			</a>
		</div>
	</div>
</div>
