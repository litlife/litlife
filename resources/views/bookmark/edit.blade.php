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


			<form id="bookmarks.update" role="form" method="POST"
				  action="{{ route('bookmarks.update', compact('bookmark')) }}">

				@csrf
				@method('patch')

				<div class="form-group{{ $errors->has('title') ? ' has-error' : '' }}">

					<label for="title" class="col-form-label">{{ __('bookmark.title') }}</label>

					<textarea id="title" class="form-control" rows="5"
							  name="title">{{ old('title') ?? $bookmark->title  }}</textarea>

				</div>

				<div class="form-group{{ $errors->has('folder_id') ? ' has-error' : '' }}">

					<label for="folder_id" class="col-form-label">{{ __('bookmark.folder_id') }}</label>

					<select name="folder_id" class="form-control">

						<option></option>
						@foreach($bookmarks_folders as $folder)
							@if ($folder->id == $bookmark->folder_id)
								<option value="{{ $folder->id }}" selected>{{ $folder->title }}</option>
							@else
								<option value="{{ $folder->id }}">{{ $folder->title }}</option>
							@endif
						@endforeach
					</select>

				</div>

				<button type="submit" class="btn btn-primary">{{ __('common.save') }}</button>


			</form>

		</div>
	</div>

@endsection