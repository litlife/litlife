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

			<form role="form" method="POST" action="{{ route('sequences.store') }}" enctype="multipart/form-data">
				@csrf


				<div class="row form-group{{ $errors->has('name') ? ' has-error' : '' }}">
					<label for="name" class="col-md-3 col-lg-2 col-form-label">{{ __('sequence.name') }}</label>
					<div class="col-md-9 col-lg-10">
						<input id="name" name="name" class="form-control" value="{{ old('name') }}"/>
					</div>
				</div>

				<div class="row form-group">
					<label for="description"
						   class="col-md-3 col-lg-2 col-form-label">{{ __('sequence.description') }}</label>
					<div class="col-md-9 col-lg-10">
                        <textarea id="description" class="editor form-control" rows="5"
								  name="description">{{ old('description')  }}</textarea>
					</div>
				</div>

				<div class="row form-group">
					<div class="col-md-9 col-lg-10 offset-md-3 offset-lg-2">
						<button type="submit" class="btn btn-primary">{{ __('common.create') }}</button>
					</div>
				</div>

			</form>
		</div>
	</div>

@endsection
