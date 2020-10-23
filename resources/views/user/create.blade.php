@extends('layouts.app')

@push('scripts')
	<script src="{{ mix('js/users.create.js', config('litlife.assets_path')) }}"></script>
@endpush

@section('content')

	@if (count($errors->registration) > 0)
		<div class="alert alert-danger">
			<ul>
				@foreach ($errors->registration->all() as $error)
					<li>{{ $error }}</li>
				@endforeach
			</ul>
		</div>
	@endif

	<div class="card">
		<div class="card-body">

			<form role="form" action="{{ route('users.store', $invitation->token) }}"
				  method="post" enctype="multipart/form-data">

				@csrf

				<div class="row form-group">
					<label for="nick" class="col-md-3 col-lg-2 col-form-label">{{ __('user.nick').' *' }}</label>
					<div class="col-md-9 col-lg-10">
						<input name="nick" type="text" id="nick"
							   class="form-control {{ $errors->registration->has('nick') ? ' is-invalid' : '' }}"
							   minlength="2" maxlength="20"
							   aria-describedby="nickHelpBlock" value="{{ old('nick') }}">
						<small class="text-danger" style="display: none;">
							{{ __('validation.user_nick_unique') }}
						</small>
						<small class="text-success" style="display: none;">
							{{ __('user.this_nick_is_not_busy') }}
						</small>
					</div>
				</div>

				<div class="row form-group">
					<label for="last_name" class="col-md-3 col-lg-2 col-form-label">{{ __('user.last_name').' *' }}</label>
					<div class="col-md-9 col-lg-10">
						<input name="last_name" type="text" id="last_name"
							   class="form-control {{ $errors->registration->has('last_name') ? ' is-invalid' : '' }}"
							   minlength="2" maxlength="20"
							   aria-describedby="lastNameHelpBlock" value="{{ old('last_name') }}">
						<small id="lastNameHelpBlock" class="form-text text-muted">
							{{ __('validation.alpha_single_quote', ['attribute' => __('user.last_name')]) }}
						</small>
					</div>
				</div>

				<div class="row form-group{{ $errors->registration->has('first_name') ? ' is-invalid' : '' }}">
					<label for="first_name" class="col-md-3 col-lg-2 col-form-label">{{ __('user.first_name').' *' }}</label>
					<div class="col-md-9 col-lg-10">
						<input name="first_name" type="text" id="first_name"
							   class="form-control {{ $errors->registration->has('first_name') ? ' is-invalid' : '' }}"
							   minlength="2" maxlength="20"
							   aria-describedby="firstNameHelpBlock" value="{{ old('first_name') }}">
						<small id="firstNameHelpBlock" class="form-text text-muted">
							{{ __('validation.alpha_single_quote', ['attribute' => __('user.first_name')]) }}
						</small>
					</div>
				</div>

				<div class="row form-group">
					<label for="middle_name" class="col-md-3 col-lg-2 col-form-label">{{ __('user.middle_name').'' }}</label>
					<div class="col-md-9 col-lg-10">
						<input name="middle_name" type="text" id="middle_name"
							   class="form-control {{ $errors->registration->has('middle_name') ? ' is-invalid' : '' }}"
							   minlength="2" maxlength="20"
							   aria-describedby="middleNameHelpBlock" value="{{ old('middle_name') }}">
						<small id="middleNameHelpBlock" class="form-text text-muted">
							{{ __('validation.alpha_single_quote', ['attribute' => __('user.middle_name')]) }}
						</small>
					</div>
				</div>

				<div class="row form-group">
					<label for="name_show_type"
						   class="col-md-3 col-lg-2 col-form-label">{{ __('user.name_show_type') }}</label>
					<div class="col-md-9 col-lg-10">
						<select id="name_show_type" name="name_show_type"
								class="form-control {{ $errors->registration->has('name_show_type') ? ' is-invalid' : '' }}">
							@foreach (\App\Enums\UserNameShowType::getKeys() as $key)
								@if ($key == (old('name_show_type')))
									<option value="{{ $key }}" selected>{{ __('user.name_show_type_array.'.$key) }}</option>
								@else
									<option value="{{ $key }}">{{ __('user.name_show_type_array.'.$key) }}</option>
								@endif
							@endforeach
						</select>
					</div>
				</div>

				<div class="row form-group">
					{{ Form::label('gender', __('user.gender').' ', ['class' => 'col-md-3 col-lg-2 col-form-label']) }}
					<div class="col-md-9 col-lg-10">
						{{ Form::select('gender', __("gender"), old('gender'), [
						'class' => 'form-control '.($errors->registration->has('gender') ? ' is-invalid' : '')]) }}
					</div>
				</div>

				<div class="row form-group">
					<label for="born_date" class="col-md-3 col-lg-2 col-form-label">
						{{ __('user.born_date') }}
					</label>
					<div class="col-md-9 col-lg-10">

						<select name="born_day" style="width:5rem;"
								class="form-control d-inline-block {{ $errors->user->has('born_day') ? ' is-invalid' : '' }}">
							<option value="">{{ __('date.select_day') }}</option>
							@for ($a = 1; $a <= 31; $a++)
								<option value="{{ $a }}"
										@if (old('born_day') == $a) selected @endif>{{ $a }}</option>
							@endfor
						</select>

						<select name="born_month" style="width:8rem;"
								class="form-control d-inline-block {{ $errors->user->has('born_month') ? ' is-invalid' : '' }}">
							<option value="">{{ __('date.select_month') }}</option>
							@for ($a = 1; $a <= 12; $a++)
								<option value="{{ $a }}"
										@if (old('born_month') == $a) selected @endif>{{ __('date.month.'.$a) }}</option>
							@endfor
						</select>

						<select name="born_year" style="width:6rem;"
								class="form-control d-inline-block {{ $errors->user->has('born_year') ? ' is-invalid' : '' }}">
							<option value="">{{ __('date.select_year') }}</option>
							@for ($a = (date('Y') - 8); $a > 1900; $a--)
								<option value="{{ $a }}"
										@if (old('born_year') == $a) selected @endif>{{ $a }}</option>
							@endfor
						</select>

					</div>
				</div>

				<div class="row form-group">
					{{ Form::label('born_date_show', __('user.born_date_show').' ', ['class' => 'col-md-3 col-lg-2 col-form-label']) }}
					<div class="col-md-9 col-lg-10">
						{{ Form::select('born_date_show', __("user.born_date_show_choices"), old('born_date_show'),
						['class' => 'form-control '.($errors->registration->has('born_date_show') ? ' is-invalid' : '')]) }}
					</div>
				</div>

				<div class="row form-group">
					<label for="password" class="col-md-3 col-lg-2 col-form-label">{{ __('user.password').' *' }}</label>
					<div class="col-md-9 col-lg-10">
						<input name="password" type="password" id="password"
							   class="form-control {{ $errors->registration->has('password') ? ' is-invalid' : '' }}"
							   required
							   aria-describedby="passwordHelpBlock" value="{{ old('password') }}">
						<small id="passwordHelpBlock" class="form-text text-muted">
							{{ __('user.password_helper', ['min' => config('litlife.min_password_length')]) }}
						</small>
					</div>
				</div>

				<div class="row form-group">
					<label for="password_confirmation"
						   class="col-md-3 col-lg-2 col-form-label">{{ __('user.password_confirmation').' *' }}</label>
					<div class="col-md-9 col-lg-10">
						<input name="password_confirmation" type="password" id="password_confirmation"
							   class="form-control {{ $errors->registration->has('password_confirmation') ? ' is-invalid' : '' }}"
							   required
							   aria-describedby="passwordConfirmationHelpBlock" value="{{ old('password_confirmation') }}">
						<small id="passwordConfirmationHelpBlock" class="form-text text-muted">
							{{ __('user.password_confirmation_helper') }}
						</small>
					</div>
				</div>

				<div class="row form-group">
					<div class="col-md-9 col-lg-10 offset-md-3 offset-lg-2">
						<button type="submit" class="btn btn-primary">
							{{ __('user.register') }}
						</button>
					</div>
				</div>

			</form>

		</div>
	</div>

@endsection