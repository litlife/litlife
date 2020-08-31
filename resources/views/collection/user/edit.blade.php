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
				  action="{{ route('collections.users.update', ['collection' => $collection, 'user' => $user]) }}"
				  enctype="multipart/form-data">
				@csrf
				@method('patch')

				<div class="form-group">
					<label for="description" class="col-form-label">
						{{ __('collection_user.description') }}
					</label>

					<textarea id="description" name="description" maxlength="100"
							  class="form-control{{ $errors->has('description') ? ' is-invalid' : '' }}">{{ old('description') ?? $collectionUser->description }}</textarea>

				</div>

				@foreach ($collectionUser->getPermissions() as $name => $value)
					<div class="form-group">
						<div class="form-check">
							<input name="{{ $name }}" type="hidden" value="0">
							<input name="{{ $name }}"
								   class="form-check-input {{ $errors->has($name) ? ' is-invalid' : '' }}"
								   type="checkbox"
								   value="1"
								   @if (old($name) ?? $value) checked="checked" @endif
								   id="{{ $name }}">
							<label for="{{ $name }}" class="form-check-label">
								{{ __('collection_user.'.$name) }}
							</label>
						</div>
					</div>
				@endforeach

				<button type="submit" class="btn btn-primary">{{ __('common.save') }}</button>

			</form>
		</div>
	</div>

@endsection
