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

			<form role="form" method="POST" action="{{ route('books.notes.store', $book) }}">

				@csrf


				<div class="row form-group{{ $errors->has('title') ? ' has-error' : '' }}">
					<div class="col-12">
						<label for="title" class="col-form-label"></label>
						<input id="title" name="title" class="form-control" type="text" placeholder="{{ __('note.title') }}"
							   value="{{ old('title') ?? ''  }}">
					</div>
				</div>

				<div class="row form-group{{ $errors->has('content') ? ' has-error' : '' }}">
					<div class="col-12">
						<label for="content" class="col-md-0 col-form-label">{{ __('note.content') }}</label>

						<textarea id="content" name="content"
								  class="ckeditor_book">{{ old('content') ?? $section->contentHandled ?? ''  }}</textarea>

						@include('ckeditor_book', ['book' => $book])
					</div>
				</div>

				<div class="row form-group">
					<div class="col-12">
						<button type="submit" class="btn btn-primary">
							{{ __('common.create') }}
						</button>
					</div>
				</div>

			</form>

		</div>
	</div>
@endsection