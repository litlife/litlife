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

			<form id="bookmark_folders.update" role="form" method="POST"
				  action="{{ route('bookmark_folders.update', ['bookmark_folder' => $bookmarkFolder]) }}">

				@csrf
				@method('patch')

				<div class="form-group{{ $errors->has('title') ? ' has-error' : '' }}">

					<label for="title" class="col-form-label">{{ __('bookmark_folder.title') }}</label>

					<textarea id="title" class="form-control" rows="5"
							  name="title">{{ old('title') ?? $bookmarkFolder->title  }}</textarea>

				</div>

				<button type="submit" class="btn btn-primary">{{ __('common.save') }}</button>


			</form>

		</div>
	</div>

@endsection