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

			<form role="form" method="POST" action="{{ route('books.files.store', compact('book')) }}"
				  enctype="multipart/form-data">

				@csrf

				<div class="form-group{{ $errors->has('file') ? ' has-error' : '' }}">
					<label for="file" class="col-form-label">{{ __('book_file.file') }}</label>

					<input id="file" name="file" type="file" required="required">

					<small id="passwordHelpBlock" class="form-text text-muted">
						{{ __('book_file.allowed_extensions') }}
						: {{ implode(', ', config('litlife.upload_allowed_file_extensions')) }} <br/>
						{{ __('book_file.online_reading_pages_and_chapters_are_extracted_only_from_book_formats') }}
						: {{ implode(', ', $fileExtensionsWhichCanExtractText) }} <br/>
						{{ __('book_file.max_size') }}: {{ ByteUnits\bytes(getMaxUploadNumberBytes())->format() }}
					</small>
				</div>

				<div class="form-group{{ $errors->has('comment') ? ' has-error' : '' }}">
					<label for="comment" class="col-form-label">{{ __('book_file.comment') }}</label>

					<textarea id="comment" name="comment" type="text" class="form-control"></textarea>
					<small class="form-text text-muted">
						{{ __('book_file.comment_helper') }}
					</small>
				</div>

				<div class="form-group{{ $errors->has('number') ? ' has-error' : '' }}">
					<label for="number" class="col-form-label">{{ __('book_file.number') }}</label>

					<input id="number" name="number" type="text" class="form-control"
						   placeholder="{{ __('book_file.number') }}"/>

					<small class="form-text text-muted">
						{{ __('book_file.number_helper') }}
					</small>
				</div>

				<button type="submit" class="btn btn-primary">
					{{ __('common.upload') }}
				</button>

				<small class="form-text text-muted">
					{{ __('book.add_for_review_confirm_rules') }}
				</small>
			</form>
		</div>
	</div>

@endsection
