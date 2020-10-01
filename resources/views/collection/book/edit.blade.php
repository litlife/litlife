@extends('layouts.app')

@push('scripts')

@endpush

@section('content')

	@include('collection.show_navbar')

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

			<form role="form" method="POST"
				  action="{{ route('collections.books.update', ['collection' => $collection, 'book' => $book]) }}"
				  enctype="multipart/form-data">
				@csrf

				<input id="book_id" name="book_id" type="hidden" value="{{ $book->id }}"/>

				<div class="row form-group">
					<label for="number" class="col-md-3 col-lg-2 col-form-label">
						{{ __('collection.number') }}
					</label>
					<div class="col-md-9 col-lg-10">
						<input id="number" name="number" type="text"
							   class="form-control{{ $errors->has('number') ? ' is-invalid' : '' }}"
							   value="{{ old('number') ?? $collected_book->number }}"/>
					</div>
				</div>

				<div class="row form-group">
					<label for="comment" class="col-md-3 col-lg-2 col-form-label">
						{{ __('collection.comment') }}
					</label>
					<div class="col-md-9 col-lg-10">
                        <textarea id="comment" name="comment" class="form-control{{ $errors->has('comment') ? ' is-invalid' : '' }}"
								  rows="5">{{ old('comment') ?? $collected_book->comment }}</textarea>
					</div>
				</div>

				<div class="row form-group">
					<div class="col-md-9 col-lg-10 offset-md-3 offset-lg-2">
						<button type="submit" class="btn btn-primary">{{ __('common.save') }}</button>
					</div>
				</div>

			</form>

		</div>
	</div>

@endsection
