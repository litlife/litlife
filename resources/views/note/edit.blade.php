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

	<div class="card">
		<div class="card-body">

			<form role="form" method="POST"
				  action="{{ route('books.notes.update', ['book' => $book, 'note' => $section->inner_id]) }}">

				@csrf

				@method('patch')

				@if (session('success'))
					<div class="alert alert-success alert-dismissable">
						{{ session('success') }}
						<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
					</div>
				@endif

				<div class="row form-group{{ $errors->has('title') ? ' has-error' : '' }}">
					<div class="col-12">
						<label for="title" class="col-form-label">{{ __('note.title') }}</label>

						<input name="title" class="form-control" type="text" placeholder="{{ __('note.title') }}"
							   value="{{ old('title') ?? $section->title ?? ''  }}">
					</div>
				</div>

				<div class="row form-group{{ $errors->has('content') ? ' has-error' : '' }}">
					<div class="col-12">

						<label for="content" class="col-form-label">{{ __('note.content') }}</label>

						@include('ckeditor_book', ['book' => $book])

						<textarea id="content" name="content"
								  class="ckeditor_book">{{ old('content') ?? $section->getContent() ?? ''  }}</textarea>
					</div>
				</div>

				<div class="row form-group">
					<div class="col-12">
						<button type="submit" class="btn btn-primary">
							{{ __('common.save') }}
						</button>
					</div>
				</div>

			</form>
		</div>
	</div>
@endsection