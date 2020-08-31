@extends('layouts.app')

@section('content')

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

			<form role="form" method="POST"
				  action="{{ route('books.replace_book_created_by_another_user', ['book' => $book]) }}">
				@csrf

				<div class="form-group">
					{{ __('book.replace_book_created_by_another_user_helper') }}
				</div>

				<div class="form-group{{ $errors->has('book_id') ? ' has-error' : '' }}">

					<input id="book_id" type="text" class="form-control"
						   placeholder="{{ __('book.enter_book_id_created_by_another_user') }}"
						   name="book_id">
				</div>

				<button type="submit" class="btn btn-primary">
					{{ __('common.replace') }}
				</button>
			</form>

		</div>
	</div>

@endsection
