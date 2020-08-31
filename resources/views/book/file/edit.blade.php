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

	<div class="card">
		<div class="card-body">

			<form role="form" method="POST" enctype="multipart/form-data"
				  action="{{ route('books.files.update', compact('file', 'book')) }}">

				@csrf
				@method('patch')



				@if (session('success'))
					<div class="alert alert-success alert-dismissable">
						{{ session('success') }}
						<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
					</div>
				@endif

				<div class="form-group{{ $errors->has('comment') ? ' has-error' : '' }}">
					<label for="comment" class="col-form-label">{{ __('book_file.comment') }}</label>
					<textarea id="comment" name="comment" type="text"
							  class="form-control">{{ old('comment') ?: $file->comment }}</textarea>
					<small class="form-text text-muted">
						{{ __('book_file.comment_helper') }}
					</small>
				</div>

				<div class="form-group{{ $errors->has('number') ? ' has-error' : '' }}">
					<label for="number" class="col-form-label">{{ __('book_file.number') }}</label>
					<input id="number" name="number" type="text" class="form-control"
						   value="{{ old('number') ?: $file->number }}"
						   placeholder="{{ __('book_file.number') }}"/>
					<small class="form-text text-muted">
						{{ __('book_file.number_helper') }}
					</small>
				</div>

				<button type="submit" class="btn btn-primary">{{ __('common.save') }}</button>

			</form>
		</div>
	</div>
@endsection