@extends('layouts.app')

@push('scripts')

	<script src="{{ mix('js/table.example.js', config('litlife.assets_path')) }}"></script>

	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.31.1/js/jquery.tablesorter.js"></script>

@endpush

@push ('css')



	<!--
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.31.1/css/theme.bootstrap_4.min.css"/>
    <link rel="stylesheet" type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/datatables/1.10.19/css/dataTables.bootstrap4.min.css"/>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.2.3/css/responsive.dataTables.min.css"/>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/fixedheader/3.1.6/css/fixedHeader.dataTables.min.css"/>
-->
@endpush

@section('content')

	<script type=text/javascript>


	</script>

	<style type=text/css>

		.small2 {
			font-size: 0.9rem !important;
			text-align: center;
		}

		.table-test thead th {
			padding-top: 1rem;
			padding-bottom: 1rem;
			background-color: #fff !important;
			cursor: pointer;
		}


	</style>

	<div class="card">
		<div class="card-body p-2">

			<div class="table-responsive">
				<table id="table" class="table table-striped table-hover table-sm table-bordered table-test mb-0"
					   data-trim-on-search="true"
					   data-mobile-responsive="true" data-toggle="table"
					   style="font-size:0.9rem; ">
					<thead class="thead-light">
					<tr>
						<th class="small2" data-sortinitialorder="asc" data-sorter="window.htmlSorter">Заголовок</th>
						<th class="small2" data-sortinitialorder="desc" data-toggle="tooltip" data-placement="top"
							title="Средняя оценка (Количество оценок)">
							Оценка
						</th>
						<th class="small2" data-sortinitialorder="desc" data-toggle="tooltip" data-placement="top"
							title="Количество комментариев">
							<i class="far fa-comments"></i>
						</th>
						@auth
							<th class="small2" data-toggle="tooltip" data-placement="top" title="Ваш статус прочтения книги">
								Статуc
							</th>
						@endauth
						<th class="small2" data-sortinitialorder="desc" data-toggle="tooltip" data-placement="top"
							title="Дата добавления книги">
							Добавлена
						</th>
						<th class="small2" data-sorter="window.htmlSorter" data-sortinitialorder="asc">Жанры</th>
						<th class="small2" data-sortinitialorder="desc"
							data-toggle="tooltip" data-placement="top" title="Количество страниц"><i class="far fa-file"></i>
						</th>
						<th class="small2" data-sorter="window.htmlSorter">Серии</th>
						<th class="small2" data-sortinitialorder="asc" data-toggle="tooltip" data-placement="top"
							title="Язык текста книги">
							Язык
						</th>
						<th class="small2" data-sortinitialorder="asc" data-toggle="tooltip" data-placement="top"
							title="Год печати книги">
							Издана
						</th>
						<th class="small2" data-sortinitialorder="asc" data-toggle="tooltip" data-placement="top"
							title="Год написания книги">
							Написана
						</th>
					</tr>
					</thead>
					<tbody>

					@foreach ($books as $book)

						<tr>
							<td>
								<h3 class="h6 font-weight-bold">
									<x-book-name :book="$book"/>
								</h3>
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
							<td data-sort="{{ $book->created_at->timestamp }}">
								<x-time :time="$book->created_at, 'hide_hour_minute' => true"/>
							</td>
							<td>
								@if ((isset($book->genres)) and ($book->genres->count()))
									@foreach ($book->genres as $genre)
										<a href="{{ route('books', ['genre' => $genre->id]) }}">{{ $genre->name }}</a>{{ $loop->last ? '' : ', ' }}
									@endforeach
								@endif
							</td>
							<td>{{ $book->page_count }}</td>
							<td data-sort="{{ $book->sequences->first() ? $book->sequences->first()->name.' '.sprintf("%010s", $book->sequences->first()->pivot->number) : 'ٴ' }}">
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

		</div>
	</div>



@endsection
