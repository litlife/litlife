@if (session('unconfirmed'))

	<div class="alert alert-danger">
		{{ __('user_email.not_confirm') }}
	</div>

@elseif (session('you_account_suspended_try_recover_password'))

	<div class="alert alert-danger">
		{{ __('auth.you_account_suspended_try_recover_password') }}
		<a class="alert-link" href="{{ route('password.request', ['email' => old('login')]) }}">
			{{ __('auth.go_to_recover_password') }}
		</a>
	</div>

@elseif (session('failed'))

	<div class="alert alert-danger">
		{{ __('auth.failed') }}
		<a class="alert-link" href="{{ route('password.request', ['email' => old('login')]) }}">
			{{ __('auth.go_to_recover_password') }}
		</a>
	</div>

@else

	@if (isset($errors) and (count($errors->login) > 0))
		<div class="alert alert-danger">
			@foreach ($errors->login->all() as $error)
				<div>{{ $error }}</div>
			@endforeach
		</div>
	@endif

@endif