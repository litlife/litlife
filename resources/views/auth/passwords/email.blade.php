@extends('layouts.app')

@section('content')

	@if (session('success'))
		<div class="alert alert-success alert-dismissable">
			{{ session('success') }}
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		</div>
	@endif

	@if (count($errors->email) > 0)
		<div class="alert alert-danger">
			<ul class="mb-0">
				@foreach ($errors->email->all() as $error)
					<li>{{ $error }}</li>
				@endforeach
			</ul>
		</div>
	@endif

	<div class="card">
		<div class="card-body">

			<form role="form" method="POST" action="{{ route('password.email') }}">
				@csrf

				<div class="row form-group{{ $errors->email->has('email') ? ' has-error' : '' }}">
					<label for="email" class="col-md-2 col-form-label">{{ __('user_email.email') }}</label>

					<div class="col-md-10">
						<input id="email" type="email" class="form-control" name="email"
							   value="{{ old('email') ?? $email }}" required>
					</div>
				</div>

				<div class="row form-group{{ $errors->email->has('g-recaptcha-response') ? ' has-error' : '' }}">
					<label for="g-recaptcha-response" class="col-md-2 col-form-label"></label>
					<div class="col-md-10">
						{!! NoCaptcha::display() !!}
						@push('scripts') {!! NoCaptcha::renderJs(''.config('locale').'') !!} @endpush
					</div>
				</div>

				<div class="row form-group">
					<div class="col-12 offset-md-2">
						<button type="submit" class="btn btn-primary ">
							{{ __('auth.send_link_to_password_restore') }}
						</button>
					</div>
				</div>
			</form>

		</div>
	</div>


@endsection
