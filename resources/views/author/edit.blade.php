@extends('layouts.app')

@push('scripts')
	<script src="{{ mix('js/authors.edit.js', config('litlife.assets_path')) }}"></script>
@endpush

@section('content')

	@include ('author.edit_tab')

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

			<form class="mb-3" role="form" action="{{ route('authors.photos.store', $author) }}"
				  method="post" enctype="multipart/form-data">

				@csrf

				<div class="row form-group{{ $errors->photo->has('file') ? ' has-error' : '' }}">
					<label for="file" class="col-md-3 col-lg-2 col-form-label">{{ __('author.photo') }}:</label>
					<div class="col-md-9 col-lg-10">
						<div class="mb-3">
							<x-author-photo :author="$author" width="200" height="200"
											class="img-fluid rounded lazyload"
											href="{{ route('authors.photo', $author) }}"/>
						</div>

						<div class="">
							<input size="{{ ByteUnits\Metric::bytes(config('litlife.max_image_size'))->numberOfBytes() }}"
								   type="file" name="file"/>
						</div>

						<small class="form-text text-muted">{{ __('common.max_size') }}
							: {{ ByteUnits\Metric::kilobytes(config('litlife.max_image_size'))->format() }}</small>
					</div>
				</div>

				<div class="row form-group{{ $errors->photo->has('file') ? ' has-error' : '' }}">
					<div class="col-md-9 col-lg-10 offset-md-3 offset-lg-2">
						<button class="btn btn-primary" type="submit">{{ __('common.upload') }}</button>
						@can('delete', $author->photo)
							<a class="btn btn-primary"
							   href="{{ route('authors.photos.delete', ['author' => $author, 'id' => $author->photo->id]) }}">
								{{ __('common.delete') }}
							</a>
						@endcan
					</div>
				</div>

			</form>

			<form role="form" class="author-edit" action="{{ route('authors.update', $author) }}"
				  method="post" enctype="multipart/form-data">

				@csrf
				@method('patch')

				<div class="row form-group{{ $errors->has('last_name') ? ' has-error' : '' }}">
					{{ Form::label('last_name', __('author.last_name').' ', ['class' => 'col-sm-2  col-form-label']) }}
					<div class="col-md-9 col-lg-10">
						{{ Form::text('last_name', old('last_name') ?: $author->last_name, ['class' => 'form-control']) }}
					</div>
				</div>

				<div class="row form-group{{ $errors->has('first_name') ? ' has-error' : '' }}">
					{{ Form::label('first_name', __('author.first_name').' ', ['class' => 'col-md-3 col-lg-2 col-form-label']) }}
					<div class="col-md-9 col-lg-10">
						{{ Form::text('first_name', old('first_name') ?: $author->first_name, ['class' => 'form-control']) }}
					</div>
				</div>

				<div class="row form-group{{ $errors->has('middle_name') ? ' has-error' : '' }}">
					{{ Form::label('middle_name', __('author.middle_name').' ', ['class' => 'col-md-3 col-lg-2 col-form-label']) }}
					<div class="col-md-9 col-lg-10">
						{{ Form::text('middle_name', old('middle_name') ?: $author->middle_name, ['class' => 'form-control']) }}
					</div>
				</div>

				<div class="row form-group{{ $errors->has('nickname') ? ' has-error' : '' }}">
					{{ Form::label('nickname', __('author.nickname').' ', ['class' => 'col-md-3 col-lg-2 col-form-label']) }}
					<div class="col-md-9 col-lg-10">
						{{ Form::text('nickname', old('nickname') ?: $author->nickname, ['class' => 'form-control']) }}
					</div>
				</div>

				<div class="row form-group{{ $errors->has('lang') ? ' has-error' : '' }}">
					<label for="lang" class="col-md-3 col-lg-2 col-form-label">{{ __('author.lang') }}</label>
					<div class="col-sm-6">

						<select name="lang" class="form-control">

							<option></option>
							@foreach(App\Language::all() as $language)

								@if ($language->code == $author->lang)
									<option value="{{ $language->code }}" selected>{{ $language->name }}
										- {{ $language->code }}</option>
								@else
									<option value="{{ $language->code }}">{{ $language->name }}
										- {{ $language->code }}</option>
								@endif

							@endforeach
						</select>
						<small class="form-text text-muted">
							{{ __('author.lang_helper') }}
						</small>
					</div>
				</div>

				<div class="row form-group{{ $errors->has('home_page') ? ' has-error' : '' }}">
					{{ Form::label('home_page', __('author.home_page').' ', ['class' => 'col-md-3 col-lg-2 col-form-label']) }}
					<div class="col-md-9 col-lg-10">
						{{ Form::text('home_page', old('home_page') ?: $author->home_page, ['class' => 'form-control']) }}
					</div>
				</div>

				<div class="row form-group{{ $errors->has('email') ? ' has-error' : '' }}">
					{{ Form::label('email', __('author.email').' ', ['class' => 'col-md-3 col-lg-2 col-form-label']) }}
					<div class="col-md-9 col-lg-10">
						{{ Form::text('email', old('email') ?: $author->email, ['class' => 'form-control']) }}
					</div>
				</div>

				<div class="row form-group{{ $errors->has('wikipedia_url') ? ' has-error' : '' }}">
					{{ Form::label('wikipedia_url', __('author.wikipedia_url').' ', ['class' => 'col-md-3 col-lg-2 col-form-label']) }}
					<div class="col-md-9 col-lg-10">
						{{ Form::text('wikipedia_url', old('wikipedia_url') ?: $author->wikipedia_url, ['class' => 'form-control']) }}
					</div>
				</div>

				<div class="row form-group{{ $errors->has('gender') ? ' has-error' : '' }}">
					{{ Form::label('gender', __('author.gender').' ', ['class' => 'col-md-3 col-lg-2 col-form-label']) }}
					<div class="col-md-9 col-lg-10">
						{{ Form::select('gender', __("gender"), old('gender') ?: $author->gender, ['class' => 'form-control']) }}
					</div>
				</div>

				<div class="row form-group{{ $errors->has('born_date') ? ' has-error' : '' }}">
					{{ Form::label('born_date', __('author.born_date').' ', ['class' => 'col-md-3 col-lg-2 col-form-label']) }}
					<div class="col-md-9 col-lg-10">
						{{ Form::text('born_date', old('born_date') ?: $author->born_date, ['class' => 'form-control']) }}
					</div>
				</div>

				@include("form_group", ['name' => 'born_place', 'item' => $author])

				@include("form_group", ['name' => 'dead_date', 'item' => $author])

				@include("form_group", ['name' => 'dead_place', 'item' => $author])

				@include("form_group", ['name' => 'years_creation', 'item' => $author])

				@include("form_group", ['name' => 'orig_last_name', 'item' => $author])
				@include("form_group", ['name' => 'orig_first_name', 'item' => $author])
				@include("form_group", ['name' => 'orig_middle_name', 'item' => $author])

				@include('ckeditor_biography', ['height' => 300])

				<div class="row form-group{{ $errors->has('biography') ? ' has-error' : '' }}">
					<label class="col-md-3 col-lg-2 col-form-label" for="biography">{{ __('author.about_author') }}</label>
					<div class="col-md-9 col-lg-10">
                        <textarea id="biography"
								  class="editor form-control{{ $errors->has('biography') ? ' is-invalid' : '' }}"
								  name="biography">{{ old('biography') ?? (!empty($author->biography) ? $author->biography->text : null) }}</textarea>
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
		{!! JsValidator::formRequest('App\Http\Requests\StoreAuthor', '.content  .author-edit') !!}

	--}}

@endsection
