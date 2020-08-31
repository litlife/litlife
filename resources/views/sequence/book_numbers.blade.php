@extends('layouts.app')

@section('content')

	<form role="form" method="POST" action="{{ route('sequences.book_numbers_save', $sequence) }}"
		  enctype="multipart/form-data">
		@csrf

		@if (session('success'))
			<div class="alert alert-success alert-dismissable">
				{{ session('success') }}
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
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

		<div class="card">
			<div class="card-body">
				@if ($books->count())

					<div class="table-responsive mb-0">
						<table class="table">
							@foreach ($books as $book)
								<tr>
									<td>
										<x-book-name :book="$book"/>
										-

										@if ((isset($book->writers)) and ($book->writers->count()))
											@foreach ($book->writers as $author)
												<x-author-name :author="$author"/>{{ $loop->last ? '' : ', ' }}
											@endforeach
										@endif
									</td>
									<td><input name="numbers[{{ $book->id }}]" class="form-control"
											   value="{{ $book->pivot->number }}"></td>
								</tr>
							@endforeach
						</table>
					</div>

					<div>
						<button type="submit" class="btn btn-primary">
							{{ __('common.save') }}
						</button>
					</div>

				@else

					<div class="alert alert-danger">{{ __('sequence.there_are_no_books_in_the_series') }}</div>

					<a href="{{ route('sequences.show', ['sequence' => $sequence]) }}" class="btn btn-light">
						{{ __('sequence.go_back_to_the_series_page') }}
					</a>

				@endif

			</div>
		</div>

	</form>

@endsection
