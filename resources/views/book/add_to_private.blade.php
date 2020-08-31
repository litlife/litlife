@extends('layouts.app')

@push('scripts')

@endpush

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
		<div class="alert alert-success alert-dismissable">
			{{ session('success') }}
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		</div>
	@endif

	<div class="card">
		<div class="card-body">
			<form role="form"
				  action="{{ route('books.add_to_private', ['book' => $book]) }}"
				  method="post" enctype="multipart/form-data">

				@csrf

				{{ __('book.book_will_be_sent_to_the_personal_library_that_published_it') }}

				<div class="form-group">
					<label for="reason_for_removal_from_publication" class="col-form-label">{{ __('book.reason_for_removal_from_publication') }}</label>
					<div class="">
						<textarea id="reason_for_removal_from_publication" name="reason_for_removal_from_publication" class="form-control"></textarea>

					</div>
				</div>

				<button type="submit" class="btn btn-primary">
					{{ __('book.remove_the_publication') }}
				</button>

			</form>
		</div>
	</div>

@endsection
