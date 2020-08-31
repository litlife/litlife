@extends('layouts.app')

@section('content')

	@if (count($errors->password_reset) > 0)
		<div class="alert alert-danger">
			<ul>
				@foreach ($errors->password_reset->all() as $error)
					<li>{{ $error }}</li>
				@endforeach
			</ul>
		</div>
	@endif

	<div class="card">
		<div class="card-body">

			<form role="form" method="POST"
				  action="{{ route('password.reset') }}">
				@csrf

				<input type="hidden" name="token" value="{{ $reset->token }}">

				<div class="row form-group{{ $errors->password_reset->has('password') ? ' has-error' : '' }}">
					<label for="password" class="col-md-2 col-form-label">{{ __('user.password') }}</label>
					<div class="col-md-10">
						<input id="password" type="password" class="form-control" name="password"
							   value="{{ old('password') }}" required>
						<small id="passwordHelpInline" class="text-muted">
							{{ __('user.password_helper', ['min' => config('litlife.min_password_length')]) }}
						</small>
					</div>
				</div>

				<div class="row form-group{{ $errors->password_reset->has('password_confirmation') ? ' has-error' : '' }}">
					<label for="password-confirm"
						   class="col-md-2 col-form-label">{{ __('user.password_confirmation') }}</label>
					<div class="col-md-10">
						<input id="password-confirm" type="password" class="form-control" name="password_confirmation"
							   value="{{ old('password_confirmation') }}" required>
						<small id="password-confirmHelpInline" class="text-muted">
							{{ __('user.password_confirmation_helper') }}
						</small>
					</div>
				</div>

				<div class="row form-group">
					<div class="col-md-10 offset-md-2">
						<button type="submit" class="btn btn-primary">
							{{ __('auth.change_password') }}
						</button>
					</div>
				</div>
			</form>
		</div>
	</div>
@endsection
