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

			<form role="form" method="POST" action="{{ route('collections.update', $collection) }}"
				  enctype="multipart/form-data">
				@csrf
				@method('patch')

				<div class="row form-group">
					<label for="title" class="col-md-3 col-lg-2 col-form-label">
						{{ __('collection.title') }}
					</label>
					<div class="col-md-9 col-lg-10">
						<input id="title" name="title" type="text"
							   class="form-control{{ $errors->has('title') ? ' is-invalid' : '' }}"
							   value="{{ old('title') ?? $collection->title }}"/>
					</div>
				</div>

				<div class="row form-group">
					<label for="description" class="col-md-3 col-lg-2 col-form-label">
						{{ __('collection.description') }}
					</label>
					<div class="col-md-9 col-lg-10">
                        <textarea id="description" name="description"
								  class="form-control{{ $errors->has('description') ? ' is-invalid' : '' }}">{{ old('description') ?? $collection->description }}</textarea>
					</div>
				</div>

				<div class="row form-group">
					<label for="status" class="col-md-3 col-lg-2 col-form-label">
						{{ __('collection.status') }}
					</label>
					<div class="col-md-9 col-lg-10">
						<select id="status" name="status" aria-describedby="statusHelpBlock"
								class="form-control{{ $errors->has('status') ? ' is-invalid' : '' }}">
							@foreach ([\App\Enums\StatusEnum::Accepted, \App\Enums\StatusEnum::Private] as $key)
								@if ($key == (old('status') ?? $collection->status))
									<option value="{{ $key }}"
											selected>{{ __('collection.status_array.'.$key) }}</option>
								@else
									<option value="{{ $key }}">{{ __('collection.status_array.'.$key) }}</option>
								@endif
							@endforeach
						</select>
						<small id="statusHelpBlock" class="form-text text-muted">
							{{ __('collection.status_helper') }}
						</small>
					</div>
				</div>

				<div class="row form-group">
					<label for="who_can_add" class="col-md-3 col-lg-2 col-form-label">
						{{ __('collection.who_can_add') }}
					</label>
					<div class="col-md-9 col-lg-10">
						<select id="who_can_add" name="who_can_add" aria-describedby="whoCanAddHelpBlock"
								class="form-control{{ $errors->has('who_can_add') ? ' is-invalid' : '' }}">
							@foreach (['me', 'everyone'] as $key)
								@if ($key == (old('who_can_add') ?? \App\Enums\UserAccountPermissionValues::getKey($collection->who_can_add)))
									<option value="{{ $key }}"
											selected>{{ __('collection.who_can_add_array.'.$key) }}</option>
								@else
									<option value="{{ $key }}">{{ __('collection.who_can_add_array.'.$key) }}</option>
								@endif
							@endforeach
						</select>
						<small id="whoCanAddHelpBlock" class="form-text text-muted">
							{{ __('collection.who_can_add_helper', ['value' => __('collection.who_can_add_array.me')]) }}
						</small>
					</div>
				</div>

				<div class="row form-group">
					<label for="who_can_comment" class="col-md-3 col-lg-2 col-form-label">
						{{ __('collection.who_can_comment') }}
					</label>
					<div class="col-md-9 col-lg-10">
						<select id="who_can_comment" name="who_can_comment"
								class="form-control{{ $errors->has('who_can_comment') ? ' is-invalid' : '' }}">
							@foreach (['me', 'everyone'] as $key)
								@if ($key == (old('who_can_comment') ?? \App\Enums\UserAccountPermissionValues::getKey($collection->who_can_comment)))
									<option value="{{ $key }}"
											selected>{{ __('collection.who_can_comment_array.'.$key) }}</option>
								@else
									<option value="{{ $key }}">{{ __('collection.who_can_comment_array.'.$key) }}</option>
								@endif
							@endforeach
						</select>
					</div>
				</div>

				<div class="row form-group">
					<label for="url" class="col-md-3 col-lg-2 col-form-label">
						{{ __('collection.url') }}
					</label>
					<div class="col-md-9 col-lg-10">
						<input id="url" name="url" type="text"
							   class="form-control{{ $errors->has('url') ? ' is-invalid' : '' }}"
							   value="{{ old('url') ?? $collection->url }}"/>
					</div>
				</div>

				<div class="row form-group">
					<label for="url_title" class="col-md-3 col-lg-2 col-form-label">
						{{ __('collection.url_title') }}
					</label>
					<div class="col-md-9 col-lg-10">
						<input id="url_title" name="url_title" type="text"
							   class="form-control{{ $errors->has('url_title') ? ' is-invalid' : '' }}"
							   value="{{ old('url_title') ?? $collection->url_title }}"/>
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

@endsection
