<div class="book card mb-3">
	<div class="card-body">
		<div class="col-12 mb-3 d-flex justify-content-center">
			<x-book-cover :book="$book" width="100" height="200" style="max-width: 100%;"/>
		</div>
		<div class="col-12 ">
			<h3 class="break-word h5">
				<x-book-name :book="$book"/>
			</h3>
			@if ((!$book->trashed()) and ($book->isHaveAccess()))

				<div>
					@switch ($period)
						@case('day')
						{{ __('book.vote_average') }}: {{ round($book->day_vote_average, 2) }}
						({{ $book->day_votes_count }})
						@break

						@case('week')
						{{ __('book.vote_average') }}: {{ round($book->week_vote_average, 2) }}
						({{ $book->week_votes_count }})
						@break

						@case('month')
						{{ __('book.vote_average') }}: {{ round($book->month_vote_average, 2) }}
						({{ $book->month_votes_count }})
						@break

						@case('quarter')
						{{ __('book.vote_average') }}: {{ round($book->quarter_vote_average, 2) }}
						({{ $book->quarter_votes_count }})
						@break

						@case('year')
						{{ __('book.vote_average') }}: {{ round($book->year_vote_average, 2) }}
						({{ $book->year_votes_count }})
						@break

					@endswitch
				</div>

				<div>
					@if ($book->page_count > 0)
						{{ __('book.page_count') }}: {{ $book->page_count }} |
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

				<div>
					@if(count($book->writers) > 0)
						{{ trans_choice('author.writers', $book->writers->count()) }}:
						@foreach ($book->writers as $author)
							<x-author-name :author="$author"/>{{ $loop->last ? '' : ', ' }}
						@endforeach
					@endif
				</div>

				<div>
					@if(count($book->genres) > 0)
						{{ trans_choice('genre.genres', $book->genres->count()) }}:
						@foreach ($book->genres as $number => $genre)
							<a href="{{ route('genres.show', ['genre' => $genre->getIdWithSlug()]) }}">{{ $genre->name }}</a>{{ $loop->last ? '' : ', ' }}
						@endforeach

					@endif
				</div>

				@if ($book->isReadAccess())
					@if (!empty($book->characters_count))
						<div>
							{{ __('book.characters_count') }}:
							{{ $book->characters_count }}
							@if (!empty($count = $book->getRememberedPageCharacterCountDifference()))
								<span class="badge badge-pill badge-success">+{{ $count }}</span>
							@endif
						</div>
					@endif
				@endif

				<div>
					@if(count($book->sequences) > 0)
						{{ trans_choice('book.sequences', $book->sequences->count()) }}:
						@foreach ($book->sequences as $sequence)
							@include('sequence.name', $sequence){{ $sequence->pivot->number ? ' #'.$sequence->pivot->number : ''}}{{ $loop->last ? '' : ', ' }}
						@endforeach
					@endif
				</div>
				@if (!empty($book->language) and $book->language->code != 'RU')
					<div>
						{{ __('book.ti_lb') }}: {{ $book->language->name }}
					</div>
				@endif

				@include('book.price')

				<div class="mt-2">

					@include('book.list.read_button_block')

				</div>

				@if (!empty($book->short_annotation) and !empty($text = strip_tags($book->short_annotation->getContent())))

					<div class="mt-3">
						@if (mb_strlen($text) > 200)
							{{ mb_substr($text, 0, 210) }}<span class="collapse"
																id="show_more_{{ $book->id }}">{{ mb_substr($text, 210) }}</span>
							<a class="text-info hide_on_collapse" data-toggle="collapse"
							   href="#show_more_{{ $book->id }}">{{ __('common.show_more') }}</a>
						@else
							{{ $text }}
						@endif
					</div>

				@endif
			@endif
		</div>
	</div>
</div>