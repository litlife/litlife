@extends('layouts.app')

@section('content')
	@if ($errors->any())
		<div class="alert alert-danger">
			<ul class="mb-0">
				@foreach ($errors->all() as $error)
					<li>{{ $error }}</li>
				@endforeach
			</ul>
		</div>
	@endif

	<div class="card">
		<div class="card-body">

			<form action="{{ route('keywords.store') }}" method="post">

				@csrf


				<div class="form-group">
					<label for="text" class="col-form-label">
						{{ __('keyword.text') }}
					</label>
					<input id="text" name="text" type="text"
						   class="form-control {{ $errors->has('text') ? ' is-invalid' : '' }}"
						   value="{{ old('text') }}">
				</div>

				<button type="submit" class="btn btn-primary">{{ __('common.create') }}</button>

			</form>
		</div>
	</div>

@endsection