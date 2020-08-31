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

	<div class="card mb-2">
		<div class="card-body">

			<form role="form" method="post" action="{{ route('achievements.update', compact('achievement')) }}"
				  enctype="multipart/form-data">
				@csrf
				@method('patch')

				<div class="row form-group{{ $errors->photo->has('image') ? ' has-error' : '' }}">
					<label for="file" class="col-md-3 col-lg-2 col-form-label">{{ __('achievement.image') }}:</label>
					<div class="col-md-9 col-lg-10">
						<div class="mb-3">
							@if (!empty($achievement->image))
								<img src="{{ $achievement->image->fullUrlMaxSize(200, 200) }}"
									 style="max-width:200px; max-height:200px"/>
							@endif
						</div>

						<div class="">
							<input id="image" class="{{ $errors->has('image') ? ' is-invalid' : '' }}"
								   name="image"
								   size="{{ ByteUnits\Metric::bytes(config('litlife.max_image_size'))->numberOfBytes() }}"
								   type="file"/>
						</div>

						<small class="form-text text-muted">
							{{ __('achievement.max_size') }}
							: {{ ByteUnits\Metric::kilobytes(config('litlife.max_image_size'))->format() }}
						</small>
					</div>
				</div>

				<div class="row form-group{{ $errors->has('title') ? ' has-error' : '' }}">
					<label for="title" class="col-md-3 col-lg-2 col-form-label">{{ __('achievement.title') }}</label>
					<div class="col-md-9 col-lg-10">
						<input id="title" name="title" class="form-control" type="text"
							   value="{{ $achievement->title ?? old('title') }}"/>
					</div>
				</div>

				<div class="row form-group{{ $errors->has('description') ? ' has-error' : '' }}">
					<label for="description"
						   class="col-md-3 col-lg-2 col-form-label">{{ __('achievement.description') }}</label>
					<div class="col-md-9 col-lg-10">
						<input id="description" name="description" class="form-control" type="text"
							   value="{{ $achievement->description ?? old('description') }}"/>
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

	{{--
		{!! JsValidator::formRequest('App\Http\Requests\StoreAuthor', '.content  form') !!}
	--}}

@endsection
