@extends('layouts.app')

@section('content')

	@if ($errors->any())
		<div class="alert alert-danger">
			<ul>
				@foreach ($errors->all() as $error)
					<li>{{ $error }}</li>
				@endforeach
			</ul>
		</div>
	@endif

	@if (session('success'))
		<div class="alert alert-success">
			{{ session('success') }}
		</div>

		@isset($books)
			@if ($books->count() > 0)
				<div class="alert alert-info">
					@foreach ($books as $book)
						@foreach ($book->writers as $author)
							<x-author-name :author="$author"/>{{ $loop->last ? '' : ', ' }}
						@endforeach
						-
						<x-book-name :book="$book"/><br/>
					@endforeach
				</div>
			@endif
		@endisset
	@endif

	<div class="card">
		<div class="card-body">
			<form role="form" method="POST" action="{{ route('books.access_by_list.disable') }}">
				@csrf

				<div class="form-group{{ $errors->has('reason_for_changing_access') ? ' has-error' : '' }}">
					<label for="text" class="col-form-label">{{ __('book.reason_for_changing_access') }}</label>

					<textarea class="form-control"
							  name="reason_for_changing_access">{{ old('reason_for_changing_access') }}</textarea>
				</div>

				<div class="form-group{{ $errors->has('text') ? ' has-error' : '' }}">
					<label for="text" class="col-form-label">{{ __('common.text') }}</label>

					<textarea class="form-control" style="height: 400px;"
							  name="text">{{ old('text') }}</textarea>
				</div>

				<button type="submit" class="btn btn-primary">
					{{ __('common.block') }}
				</button>

			</form>
		</div>
	</div>

@endsection
