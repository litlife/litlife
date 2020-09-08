@extends('layouts.app')


@section('content')


	@include ('book.edit_tab')


	@if ($errors->any())
		<div class="alert alert-danger">
			<ul>
				@foreach ($errors->all() as $error)
					<li>{{ $error }}</li>
				@endforeach
			</ul>
		</div>
	@endif

	@if (session('show_how_to_attach_a_file'))
		<div class="alert alert-info">
			<i class="fas fa-info"></i> &nbsp;
			<a href="{{ route('faq') }}#how_attach_a_book_file_to_an_existing_book_page" class="alert-link" target="_blank">
				{{ __('Click to find out how to attach a file') }}
			</a>
		</div>
	@endif

	@if (session('success'))
		<div class="alert alert-success alert-dismissable">
			{{ session('success') }}
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		</div>
	@endif

	@if ($book->isForSale() and $book->isReadOrDownloadAccess())
		<div class="alert alert-warning">
			{{ __('book.book_will_be_removed_from_sale_if_you_remove_access_to_reading_and_downloading') }}
		</div>
	@endif

	<div class="card">
		<div class="card-body">

			<form role="form" method="POST" action="{{ route('books.access.save', $book) }}">
				@csrf

				<div class="form-group">
					<div class="form-check">
						<input name="read_access" type="hidden" value="0"/>
						<input id="read_access" name="read_access" class="form-check-input" type="checkbox" value="1"
							   @if (old('read_access') ?: $book->isReadAccess()) checked @endif>
						<label class="form-check-label" for="read_access">
							{{ __('book.read_access') }}
						</label>
						<small id="read_accessHelp" class="form-text text-muted">
							{{ __('book.check_the_box_to_enable_access_to_reading_the_book') }}
						</small>
					</div>
				</div>

				<div class="form-group">
					<div class="form-check">
						<input name="download_access" type="hidden" value="0"/>
						<input id="download_access" name="download_access" class="form-check-input" type="checkbox" value="1"
							   @if (old('download_access') ?: $book->isDownloadAccess()) checked @endif>
						<label class="form-check-label" for="download_access">
							{{ __('book.download_access') }}
						</label>
						<small id="download_accessHelp" class="form-text text-muted">
							{{ __('book.check_the_box_to_enable_access_to_download_the_book') }}
						</small>
					</div>
				</div>

				<div class="form-group{{ $errors->has('secret_hide_reason') ? ' has-error' : '' }}">
					<label for="secret_hide_reason" class="col-form-label">{{ __('book.secret_hide_reason') }}</label>

					<textarea id="secret_hide_reason" class="form-control"
							  name="secret_hide_reason">{{ old('secret_hide_reason') ?: $book->secret_hide_reason }}</textarea>
				</div>

				<button type="submit" class="btn btn-primary">
					{{ __('common.save') }}
				</button>

			</form>

		</div>
	</div>

@endsection
