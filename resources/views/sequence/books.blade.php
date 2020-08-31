@push('body_append')

	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.31.2/js/jquery.tablesorter.js"></script>
	<script
			src="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.31.2/js/widgets/widget-filter.min.js"></script>

@endpush

@push('css')
	<style type=text/css>

		#books .table {
			font-size: 0.9rem;
		}

		#books .table thead th {
			padding-top: 1rem;
			padding-bottom: 1rem;
			background-color: #fff !important;
			cursor: pointer;
			font-size: 0.9rem !important;
			text-align: center;
		}

		#books .table .filtered {
			display: none;
		}

		#books .table .tablesorter-errorRow td {
			text-align: center;
			cursor: pointer;
			background-color: #e6bf99;
		}

	</style>
@endpush

<div class="mb-1 d-flex flex-row align-items-center">
	<input class="search form-control" type="search" data-column="all"
		   style="max-width:300px;"
		   placeholder="{{ __('sequence.search_in_the_table') }}"/>
</div>

<div class="table-responsive mt-2">
	<table class="table table-striped table-hover table-sm table-bordered mb-0">
		<thead>
		<tr>
			<th data-sortinitialorder="asc">№</th>
			<th data-sortinitialorder="asc">{{ __('book.short.title') }}</th>
			<th data-sortinitialorder="desc">
				{{ __('book.short.vote_average') }}
			</th>
			<th data-sortinitialorder="desc" data-toggle="tooltip" data-placement="top"
				title="{{ __('book.tooltips.comments_count') }}"><i class="far fa-comments"></i></th>
			@auth
				<th data-toggle="tooltip" data-placement="top" title="{{ __('book.tooltips.user_read_status') }}">
					{{ __('book.short.user_read_status') }}
				</th>
			@endauth
			<th data-sortinitialorder="asc" data-toggle="tooltip" data-placement="top"
				title="{{ __('book.tooltips.created_at') }}">
				{{ __('book.short.created_at') }}
			</th>
			<th data-sortinitialorder="asc">{{ trans_choice('genre.genres', 2) }}</th>
			<th data-sortinitialorder="asc" data-toggle="tooltip" data-placement="top"
				title="{{ __('book.tooltips.pages_count') }}">
				<i class="far fa-file"></i>
			</th>
			<th data-sortinitialorder="asc">{{ trans_choice('author.writers', 2) }}</th>
			<th data-sortinitialorder="asc" data-toggle="tooltip" data-placement="top"
				title="{{ __('book.tooltips.ti_lb') }}">
				{{ __('book.short.ti_lb') }}
			</th>
			<th data-sortinitialorder="asc" data-toggle="tooltip" data-placement="top"
				title="{{ __('book.tooltips.pi_year') }}">
				{{ __('book.short.pi_year') }}
			</th>
			<th data-sortinitialorder="asc" data-toggle="tooltip" data-placement="top"
				title="{{ __('book.tooltips.year_writing') }}">
				{{ __('book.short.year_writing') }}
			</th>
		</tr>
		</thead>
		<tbody>
		@foreach ($books as $book)
			<tr>
				<td>{{ $book->pivot->number ? $book->pivot->number : ''}}</td>
				<td style="{{ (!$book->isReadAccess() and !$book->isDownloadAccess()) ? 'opacity: 0.6;' : '' }}">
					<h3 class="h6 font-weight-bold"
						style="{{ (!$book->isReadAccess() and !$book->isDownloadAccess()) ? 'opacity: 0.6;' : '' }}">
						<x-book-name :book="$book"/>
					</h3>
					@if ($book->isForSale())
						<i class="fas fa-coins" style="color:orange"></i>
					@endif
				</td>
				<td>{{ $book->getVoteAverageForTable() }} ({{ $book->user_vote_count }})</td>
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
				<td>
					@if ((isset($book->writers)) and ($book->writers->count()))
						@foreach ($book->writers as $author)
							<x-author-name :author="$author"/>{{ $loop->last ? '' : ', ' }}
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
