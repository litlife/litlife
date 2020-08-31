@extends('layouts.app')

@section('content')

	@if (session('success'))
		<div class="row">
			<div class="col-12">
				<div class="alert alert-success alert-dismissable">
					{{ session('success') }}
					<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
				</div>
			</div>
		</div>
	@endif

	<div class="card mb-3">
		<div class="card-body">

			@if (!empty($books) and $books->count() > 0)
				<div class="table-responsive">
					<table class="table table-stripped">
						@foreach ($books as $book)
							<tr>
								<td>
									<a href="{{ route('books.show', $book) }}">{{ $book->title }}</a>
								</td>
								<td>
									@can ('ungroup', $book)
										<a class="btn btn-light" href="{{ route('books.group.detach', $book) }}">
											{{ __('book_group.detach') }}
										</a>
									@endcan
								</td>
								<td>
									@if ($book->isMainInGroup())
										{{ __('book_group.main_book') }}
									@else
										@can ('make_main_in_group', $book)
											<a class="btn btn-light" href="{{ route('books.group.make_main_in_group', $book) }}">
												{{ __('book_group.make_main') }}
											</a>
										@endcan
									@endif
								</td>
							</tr>
						@endforeach
					</table>
				</div>
			@else
				<div class="alert alert-info mb-0">{{ __('book.nothing_found') }}</div>
			@endif
		</div>
	</div>

	@can ('group', $main_book)

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

				<form action="{{ route('books.group.attach', ['book' => $main_book]) }}" role="form"
					  method="POST">
					@csrf
					<div class="row form-group{{ $errors->has('edition_id') ? ' has-error' : '' }}">
						<div class="col-sm-12">
							<input class="form-control" name="edition_id" type="text" value="{{ old('edition_id') }}"
								   placeholder="{{ __('book_group.enter_the_book_id') }}"/>
						</div>
					</div>

					<div class="row form-group">
						<div class="col-12">
							<button type="submit" class="btn btn-primary">
								{{ __('book_group.attach') }}
							</button>
						</div>
					</div>
				</form>

			</div>
		</div>
	@endcan

@endsection