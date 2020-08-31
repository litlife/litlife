@extends('layouts.app')


@section('content')

	@include('book.create.tab')

	@if (session('success'))
		<div class="alert alert-success alert-dismissable">
			{{ session('success') }}
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		</div>
	@endif

	@if (session('file_upload_complete'))
		<div class="alert alert-success alert-dismissable">
			{{ __('book_file.upload_success') }}.
			{{ __('book_file.go_to_page_of_uploaded_book_or_uploaded_another_book') }}
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

	<div class="card mb-3">
		<div class="card-header">
			{{ __('book.option1_adding_a_book_with_a_file') }}
		</div>
		<div class="card-body ">

			<div class="mb-3">
				{{ __('book.this_option_should_be_selected_if_you_have_a_book_file') }}
			</div>

			<form role="form" method="POST" action="{{ route('books.store') }}" enctype="multipart/form-data">

				@csrf

				<div class="row form-group{{ $errors->has('file') ? ' has-error' : '' }}">
					<label for="file" class="col-md-2 col-form-label">{{ trans_choice('book_file.book_files', 1) }}</label>
					<div class="col-md-10">
						<input name="file" type="file" required="required">

						<small id="fileHelpBlock" class="form-text text-muted">
							{{ __('book_file.allowed_extensions') }}
							: {{ implode(', ', config('litlife.upload_allowed_file_extensions')) }} <br/>
							{{ __('book_file.online_reading_pages_and_chapters_are_extracted_only_from_book_formats') }}
							: {{ implode(', ', $fileExtensionsWhichCanExtractText) }} <br/>
							{{ __('book_file.max_size') }}: {{ ByteUnits\bytes(getMaxUploadNumberBytes())->format() }}
						</small>

					</div>
				</div>

				<div class="row form-group">
					<div class="col-12 offset-md-2">
						<button type="submit" class="btn btn-primary">
							{{ __('common.upload') }}
						</button>
					</div>
				</div>
			</form>
		</div>
	</div>

	<div class="card">
		<div class="card-header">
			{{ __('book.option2_add_a_book_without_a_file') }}
		</div>
		<div class="card-body">
			{{ __('book.if_you_dont_have_a_book_file_you_can_only_add_a_description_of_the_book') }}

			<form class="mt-4" role="form" method="POST" action="{{ route('books.store') }}">
				@csrf

				<div class="row form-group{{ $errors->has('title') ? ' has-error' : '' }}">
					<label for="title" class="col-md-2 col-form-label">{{ __('book.title') }}</label>

					<div class="col-md-10">
						<input id="title" type="text" class="form-control" name="title">
					</div>
				</div>

				<div class="row form-group">
					<div class="col-12 offset-md-2">
						<button type="submit" class="btn btn-primary">
							{{ __('common.create') }}
						</button>
					</div>
				</div>
			</form>

		</div>
	</div>




@endsection
