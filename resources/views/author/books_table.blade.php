<div class="books_container">
	<div class="mb-1 d-flex flex-row align-items-center">
		<h5 class="text-nowrap flex-grow-1 ml-3 mb-0">{{ $name }}</h5>
		<input class="search ml-3 form-control" type="search" data-column="all"
			   style="max-width:300px;"
			   placeholder="{{ __('author.search_in_the_table') }}"/>
	</div>
	<div class="table-responsive mt-2">
		<table class="table table-striped table-hover table-sm table-bordered mb-0">
			<thead class="thead-light">
			<tr>
				<th data-sortinitialorder="asc">{{ __('book.short.title') }}</th>
				<th data-sortinitialorder="desc" data-toggle="tooltip" data-placement="top" data-placeholder="Exact matches only"
					title="{{ __('book.tooltips.vote_average') }}">{{ __('book.short.vote_average') }}</th>
				<th data-sortinitialorder="desc" data-toggle="tooltip" data-placement="top"
					title="{{ __('book.tooltips.comments_count') }}"><i class="far fa-comments"></i></th>
				@auth
					<th data-toggle="tooltip" data-placement="top" title="{{ __('book.tooltips.user_read_status') }}">
						{{ __('book.short.user_read_status') }}
					</th>
				@endauth
				<th data-sortinitialorder="desc" data-toggle="tooltip" data-placement="top"
					title="{{ __('book.tooltips.created_at') }}" class="sorter-shortDate dateFormat-ddmmyyyy">
					{{ __('book.short.created_at') }}
				</th>
				<th data-sortinitialorder="desc">
					{{ __('book.short.genres') }}
				</th>
				<th data-sortinitialorder="desc" data-toggle="tooltip" data-placement="top"
					title="{{ __('book.tooltips.pages_count') }}">
					<i class="far fa-file"></i>
				</th>
				<th data-sorter="window.htmlSorter">
					{{ __('book.short.sequences') }}
				</th>
				<th data-sortinitialorder="asc" data-toggle="tooltip" data-placement="top"
					title="{{ __('book.tooltips.ti_lb') }}">
					{{ __('book.short.ti_lb') }}
				</th>
				<th data-sortinitialorder="asc" data-toggle="tooltip" data-placement="top"
					title="{{ __('book.tooltips.pi_year') }}">
					{{ __('book.short.pi_year') }}
				</th>
				<th data-sortinitialorder="asc" data-toggle="tooltip" data-placement="top"
					title="{{ __('book.tooltips.year_writing') }}">{{ __('book.short.year_writing') }}
				</th>
			</tr>
			</thead>
			<tbody>

			@foreach ($books as $book)

				<tr>
					<td>
						<h3 class="h6 font-weight-bold"
							style="{{ (!$book->isReadAccess() and !$book->isDownloadAccess()) ? 'opacity: 0.6;' : '' }}">
							<x-book-name :book="$book"/>
							@if ($book->isForSale())
								<i class="fas fa-coins" style="color:orange"></i>
							@endif
						</h3>
					</td>
					<td>
						{{ $book->getVoteAverageForTable() }}
						({{ $book->user_vote_count }})
					</td>
					<td>{{ $book->comment_count }}</td>
					@auth
						<td>
							@if (!empty($status = $book->statuses->first()))
								@if (!empty($status->status))
									{{ trans_choice('book.read_status_array.'.$status->status, 1)  }}
								@endif
							@endif
						</td>
					@endauth
					<td data-text="{{ $book->created_at->timestamp }}">
						<x-time :time="$book->created_at, 'hide_hour_minute' => true"/>
					</td>
					<td>
						@if ((isset($book->genres)) and ($book->genres->count()))
							@foreach ($book->genres as $genre)
								<a href="{{ route('genres.show', ['genre' => $genre->getIdWithSlug()]) }}">{{ $genre->name }}</a>{{ $loop->last ? '' : ', ' }}
							@endforeach
						@endif
					</td>
					<td>{{ $book->page_count }}</td>
					<td data-text="{{ $book->sequences->first() ? $book->sequences->first()->name.' '.sprintf("%010s", $book->sequences->first()->pivot->number) : 'ٴ' }}">
						@if ((isset($book->sequences)) and ($book->sequences->count()))
							@foreach ($book->sequences as $sequence)
								@include('sequence.name', $sequence){{ $sequence->pivot->number ? ' #'.$sequence->pivot_number : ''}}{{ $loop->last ? '' : ', ' }}
							@endforeach
						@endif
					</td>
					<td>
						@if (!empty($book->language))
							{{ $book->language->code }}
						@endif
					</td>
					<td>{{ $book->pi_year }}</td>
					<td>{{ $book->year_writing }}</td>
				</tr>

			@endforeach

			</tbody>
		</table>
	</div>
	<div class="mb-1"></div>
</div>