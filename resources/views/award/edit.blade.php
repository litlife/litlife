@extends('layouts.app')

@push('scripts')

@endpush

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


			<form role="form" method="post" action="{{ route('awards.update', compact('award')) }}"
				  enctype="multipart/form-data">
				@csrf
				@method('patch')


				<div class="row form-group{{ $errors->has('title') ? ' has-error' : '' }}">
					<label for="title" class="col-md-3 col-lg-2 col-form-label">{{ __('award.title') }}</label>
					<div class="col-md-9 col-lg-10">
						<input id="title" name="title" class="form-control" type="text"
							   value="{{ $award->title ?? old('title') }}"/>
					</div>
				</div>

				<div class="row form-group{{ $errors->has('description') ? ' has-error' : '' }}">
					<label for="description" class="col-md-3 col-lg-2 col-form-label">{{ __('award.description') }}</label>
					<div class="col-md-9 col-lg-10">
						<input id="description" name="description" class="form-control" type="text"
							   value="{{ $award->description ?? old('description') }}"/>
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
