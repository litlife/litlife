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

			<form role="form" action="{{ route('ad_blocks.store') }}"
				  method="post" enctype="multipart/form-data">

				@csrf

				<div class="form-group">

					<label for="name" class="col-form-label">{{ __('ad_block.name') }}</label>

					<textarea name="name" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}"
							  rows="1">{{ old('name') }}</textarea>
				</div>

				<div class="form-group">

					<label for="code" class="col-form-label">{{ __('ad_block.code') }}</label>

					<textarea name="code" class="form-control{{ $errors->has('code') ? ' is-invalid' : '' }}"
							  rows="5">{{ old('code') }}</textarea>
				</div>

				<div class="form-group">

					<label for="description" class="col-form-label">{{ __('ad_block.description') }}</label>

					<textarea name="description" class="form-control{{ $errors->has('description') ? ' is-invalid' : '' }}"
							  rows="5">{{ old('description') }}</textarea>
				</div>

				<button type="submit" class="btn btn-primary">{{ __('Create') }}</button>

			</form>
		</div>
	</div>

@endsection