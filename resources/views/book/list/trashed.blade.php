@if(count($books) > 0)


	@if ($books->hasPages())
		{{ $books->appends(request()->except(['page', 'ajax']))->links() }}
	@endif



	@foreach ($books as $book)

		<div class="card mb-3" data-id="{{ $book->id }}">
			<div class="card-body">
				<div class="row">
					<div class="col-md-12 col-lg-4 col-xl-3 text-center mb-3">
						<x-book-cover :book="$book" width="150" height="200" showEvenIfTrashed="1" style="max-width: 100%;"/>
					</div>
					<div class="col-md-12 col-lg-8 col-xl-9">
						<h5 class="break-word">
							<x-book-name :book="$book" showEvenIfTrashed="1"/>
						</h5>

						<div>
							@if (!empty($book->quarter_vote_avg))
								<span class="font-weight-bold small">{{ __('book.quarter_vote_avg') }}:</span>
								<span class="font-weight-normal">
                                    {{ round($book->quarter_vote_avg, 2) }} ({{ $book->quarter_vote_count }}) |
                                </span>
							@endif

							@if (!empty($book->month_vote_avg))
								<span class="font-weight-bold small">{{ __('book.month_vote_avg') }}:</span>
								<span class="font-weight-normal">
                                        {{ round($book->month_vote_avg, 2) }} ({{ $book->month_vote_count }}) |
                                    </span>
							@endif
							<span class="font-weight-bold small">{{ __('book.vote_average') }}:</span> <span
									class="font-weight-normal">{{ $book->getVoteAverageForTable() }}</span>
							({{ $book->user_vote_count }})
							@if (!empty($book->male_vote_count))
								<span class="color-blue">{{ $book->male_vote_count }}</span>
							@endif

							@if (!empty($book->female_vote_count))
								<span class="color-pink">{{ $book->female_vote_count }}</span>
							@endif
						</div>

						<div>
							@if($writers = $book->getAuthorsWithType('writers'))
								<span class="font-weight-bold small">{{ trans_choice('author.writers', $writers->count()) }}:</span>
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

						@if ($log = $book->latestActivitiesItemDeleted->first())
							<div>
								<span class="font-weight-bold small">{{ __('book.deleted_by_user') }}:</span>
								<x-user-name :user="$log->causer"/>
								<x-time :time="$log->created_at"/>
							</div>
						@endif

						<div style="margin-top:10px">

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
					</div>
				</div>
			</div>
		</div>
	@endforeach



	@if ($books->hasPages())
		{{ $books->appends(request()->except(['page', 'ajax']))->links() }}
	@endif


@else

	<div class="alert alert-info">
		{{ __('book.nothing_found') }}
	</div>
@endif


