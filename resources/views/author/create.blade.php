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


			<form id="" class="sisyphus" name="sisyphus.author.create" role="form" method="POST"
				  action="{{ route('authors.store') }}"
				  enctype="multipart/form-data">
				@csrf

				<input name="sisyphus" type="hidden" value="sisyphus.author.create"/>

				<div class="row form-group{{ $errors->has('last_name') ? ' has-error' : '' }}">
					{{ Form::label('home_page', __('author.last_name').' ', ['class' => 'col-md-3 col-lg-2 col-form-label']) }}
					<div class="col-md-9 col-lg-10">
						{{ Form::text('last_name', old('last_name'), ['class' => 'form-control'.($errors->has('last_name') ? ' is-invalid' : '')]) }}
					</div>
				</div>

				<div class="row form-group{{ $errors->has('first_name') ? ' has-error' : '' }}">
					{{ Form::label('home_page', __('author.first_name').' ', ['class' => 'col-md-3 col-lg-2 col-form-label']) }}
					<div class="col-md-9 col-lg-10">
						{{ Form::text('first_name', old('first_name'), ['class' => 'form-control'.($errors->has('first_name') ? ' is-invalid' : '')]) }}
					</div>
				</div>

				<div class="row form-group{{ $errors->has('middle_name') ? ' has-error' : '' }}">
					{{ Form::label('home_page', __('author.middle_name').' ', ['class' => 'col-md-3 col-lg-2 col-form-label']) }}
					<div class="col-md-9 col-lg-10">
						{{ Form::text('middle_name', old('middle_name'), ['class' => 'form-control'.($errors->has('middle_name') ? ' is-invalid' : '')]) }}
					</div>
				</div>

				<div class="row form-group{{ $errors->has('nickname') ? ' has-error' : '' }}">
					{{ Form::label('home_page', __('author.nickname').' ', ['class' => 'col-md-3 col-lg-2 col-form-label']) }}
					<div class="col-md-9 col-lg-10">
						{{ Form::text('nickname', old('nickname'), ['class' => 'form-control'.($errors->has('nickname') ? ' is-invalid' : '')]) }}
					</div>
				</div>


				<div class="row form-group{{ $errors->has('lang') ? ' has-error' : '' }}">
					<label for="lang" class="col-md-3 col-lg-2 col-form-label">{{ __('author.lang') }} </label>
					<div class="col-sm-6">

						<select id="lang" name="lang" class="form-control{{ $errors->has('lang') ? ' is-invalid' : '' }}">

							<option></option>
							@foreach(App\Language::all() as $language)

								@if ($language->code == old('lang'))
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
						{{ Form::text('home_page', old('home_page'), ['class' => 'form-control'.($errors->has('home_page') ? ' is-invalid' : '')]) }}
					</div>
				</div>

				<div class="row form-group{{ $errors->has('email') ? ' has-error' : '' }}">
					{{ Form::label('email', __('author.email'), ['class' => 'col-md-3 col-lg-2 col-form-label']) }}
					<div class="col-md-9 col-lg-10">
						{{ Form::text('email', old('email'), ['class' => 'form-control'.($errors->has('email') ? ' is-invalid' : '')]) }}
					</div>
				</div>

				<div class="row form-group{{ $errors->has('wikipedia_url') ? ' has-error' : '' }}">
					{{ Form::label('email', __('author.wikipedia_url').' ', ['class' => 'col-md-3 col-lg-2 col-form-label']) }}
					<div class="col-md-9 col-lg-10">
						{{ Form::text('wikipedia_url', old('wikipedia_url'), ['class' => 'form-control'.($errors->has('wikipedia_url') ? ' is-invalid' : '')]) }}
					</div>
				</div>

				<div class="row form-group{{ $errors->has('gender') ? ' has-error' : '' }}">
					{{ Form::label('gender', __('author.gender').' ', ['class' => 'col-md-3 col-lg-2 col-form-label']) }}
					<div class="col-md-9 col-lg-10">
						{{ Form::select('gender', __("gender"), old('gender') ?? 'unknown', ['class' => 'form-control'.($errors->has('gender') ? ' is-invalid' : '')]) }}
					</div>
				</div>

				<div class="row form-group{{ $errors->has('born_date') ? ' has-error' : '' }}">
					{{ Form::label('born_date', __('author.born_date').' ', ['class' => 'col-md-3 col-lg-2 col-form-label']) }}
					<div class="col-md-9 col-lg-10">
						{{ Form::text('born_date', old('born_date'), ['class' => 'form-control'.($errors->has('born_date') ? ' is-invalid' : '')]) }}
					</div>
				</div>

				<div class="row form-group{{ $errors->has('born_place') ? ' has-error' : '' }}">
					<label for="born_place" class="col-md-3 col-lg-2 col-form-label">{{ __('author.born_place') }}</label>
					<div class="col-md-9 col-lg-10">
						<input id="born_place" name="born_place"
							   class="form-control{{ $errors->has('born_place') ? ' is-invalid' : '' }}"
							   value="{{ old('born_place') }}"/>
					</div>
				</div>

				<div class="row form-group{{ $errors->has('dead_date') ? ' has-error' : '' }}">
					<label for="dead_date" class="col-md-3 col-lg-2 col-form-label">{{ __('author.dead_date') }}</label>
					<div class="col-md-9 col-lg-10">
						<input id="dead_date" name="dead_date"
							   class="form-control{{ $errors->has('dead_date') ? ' is-invalid' : '' }}"
							   value="{{ old('dead_date') }}"/>
					</div>
				</div>

				<div class="row form-group{{ $errors->has('dead_place') ? ' has-error' : '' }}">
					<label for="dead_place" class="col-md-3 col-lg-2 col-form-label">{{ __('author.dead_place') }}</label>
					<div class="col-md-9 col-lg-10">
						<input id="dead_place" name="dead_place"
							   class="form-control{{ $errors->has('dead_place') ? ' is-invalid' : '' }}"
							   value="{{ old('dead_place') }}"/>
					</div>
				</div>

				<div class="row form-group{{ $errors->has('years_creation') ? ' has-error' : '' }}">
					<label for="years_creation"
						   class="col-md-3 col-lg-2 col-form-label">{{ __('author.years_creation') }}</label>
					<div class="col-md-9 col-lg-10">
						<input id="years_creation" name="years_creation"
							   class="form-control{{ $errors->has('years_creation') ? ' is-invalid' : '' }}"
							   value="{{ old('years_creation') }}"/>
					</div>
				</div>

				<div class="row form-group{{ $errors->has('orig_last_name') ? ' has-error' : '' }}">
					<label for="orig_last_name"
						   class="col-md-3 col-lg-2 col-form-label">{{ __('author.orig_last_name') }}</label>
					<div class="col-md-9 col-lg-10">
						<input id="orig_last_name" name="orig_last_name"
							   class="form-control{{ $errors->has('orig_last_name') ? ' is-invalid' : '' }}"
							   value="{{ old('orig_last_name') }}"/>
					</div>
				</div>
				<div class="row form-group{{ $errors->has('orig_first_name') ? ' has-error' : '' }}">
					<label for="orig_first_name"
						   class="col-md-3 col-lg-2 col-form-label">{{ __('author.orig_first_name') }}</label>
					<div class="col-md-9 col-lg-10">
						<input id="orig_first_name" name="orig_first_name"
							   class="form-control{{ $errors->has('orig_first_name') ? ' is-invalid' : '' }}"
							   value="{{ old('orig_first_name') }}"/>
					</div>
				</div>
				<div class="row form-group{{ $errors->has('orig_middle_name') ? ' has-error' : '' }}">
					<label for="orig_middle_name"
						   class="col-md-3 col-lg-2 col-form-label">{{ __('author.orig_middle_name') }}</label>
					<div class="col-md-9 col-lg-10">
						<input id="orig_middle_name" name="orig_middle_name"
							   class="form-control{{ $errors->has('orig_middle_name') ? ' is-invalid' : '' }}"
							   value="{{ old('orig_middle_name') }}"/>
					</div>
				</div>

				@include('ckeditor_biography')

				<div class="row form-group{{ $errors->has('biography') ? ' has-error' : '' }}">
					<label class="col-md-3 col-lg-2 col-form-label" for="biography">{{ __('author.biography') }}</label>
					<div class="col-md-9 col-lg-10">
                        <textarea id="biography"
								  class="editor form-control{{ $errors->has('biography') ? ' is-invalid' : '' }}"
								  rows="5"
								  name="biography">{{ old('biography') }}</textarea>
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

	{{--
		{!! JsValidator::formRequest('App\Http\Requests\StoreAuthor', '.content  form') !!}
	--}}

@endsection
