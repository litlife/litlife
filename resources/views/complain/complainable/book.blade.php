<div class="card mb-3" data-id="{{ $book->id }}">
	<div class="card-body">
		<div class="row">
			<div class="col-md-12 col-lg-4 col-xl-3 text-center mb-3">
				<x-book-cover :book="$book" width="150" height="200" style="max-width: 100%;"/>
			</div>
			<div class="col-md-12 col-lg-8 col-xl-9">
				<h3 class="break-word h5">
					<x-book-name :book="$book"/>
				</h3>

				@if ((!$book->trashed()) and ($book->isHaveAccess()))

					@if ($book->isInGroup())
						<div>
							<a href="{{ route('books.editions.index', ['book' => $book->main_book_id ?? $book->id]) }}">
								{{ __('book.all_book_in_group') }}
								({{ $book->editions_count }})
							</a>
						</div>
					@endif

					<div>
						@if($writers = $book->getAuthorsWithType('writers'))
							<span class="font-weight-bold small">{{ trans_choice('author.authors', $writers->count()) }}:</span>
							<span class="font-weight-normal">
                                @foreach ($writers as $author)
									<x-author-name :author="$author"/>{{ $loop->last ? '' : ', ' }}
								@endforeach
                                </span>
						@endif
					</div>

					<div>
						@if(count($book->genres) > 0)
							<span class="font-weight-bold small">{{ trans_choice('genre.genres', $book->genres->count()) }}:</span>
							<span class="font-weight-normal">
                                @foreach ($book->genres as $number => $genre)
									<a href="{{ route('genres.show', ['genre' => $genre->getIdWithSlug()]) }}">{{ $genre->name }}</a>{{ $loop->last ? '' : ', ' }}
								@endforeach
                                </span>
						@endif
					</div>

					<div>
						@if ($book->page_count > 0)
							<span class="font-weight-bold small">{{ __('book.page_count') }}:</span> {{ $book->page_count }}
						@endif

						@if (!empty($book->ready_status))
							@switch ($book->ready_status)
								@case ('complete')
								<div class="text-success d-inline-block">
									{{ __('book.'.$book->ready_status) }}
									@if ($book->isPostedFreeFragment())
										({{ __('book.free_published_fragment')}})
									@endif
								</div>
								@break
								@case ('complete_but_publish_only_part')
								<span class="text-info">{{ __('book.'.$book->ready_status) }}</span>
								@break
								@case ('not_complete_but_still_writing')
								<span class="text-info">{{ __('book.'.$book->ready_status) }}</span>
								@break
								@case ('not_complete_and_not_will_be')
								<span class="text-info">{{ __('book.'.$book->ready_status) }}</span>
								@break

							@endswitch
						@endif
					</div>

					@if ($book->isReadAccess())
						@if (!empty($book->characters_count))
							<div>
								<span class="font-weight-bold small">{{ __('book.characters_count') }}:</span>
								{{ $book->characters_count }}

								@if (!empty($count = $book->getRememberedPageCharacterCountDifference()))
									<span class="badge badge-pill badge-success">+{{ $count }}</span>
								@endif
							</div>
						@endif
					@endif

					<div>
						@if(count($book->sequences) > 0)
							<span class="font-weight-bold small">{{ trans_choice('book.sequences', $book->sequences->count()) }}:</span>
							@foreach ($book->sequences as $sequence)
								@include('sequence.name', $sequence){{ $sequence->pivot->number ? ' #'.$sequence->pivot->number : ''}}{{ $loop->last ? '' : ', ' }}
							@endforeach
						@endif
					</div>

					@if (!empty($book->language) and $book->language->code != 'RU')
						<div>
							<span class="font-weight-bold small">{{ __('book.ti_lb') }}:</span> {{ $book->language->name }}
						</div>
					@endif

					@include('book.price')
				@endif
			</div>
		</div>
	</div>
</div>
