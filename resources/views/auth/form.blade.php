@include('auth.errors')

<form class="mb-4" action="{{ route('login') }}" method="POST">

	@csrf

	<div class="mt-0 form-group">
		<input id="login" name="login" placeholder="{{ __('auth.login') }}"
			   type="text" value="{{ old('login') }}"
			   class="form-control{{ (isset($errors) and $errors->login->has('login')) ? ' is-invalid' : '' }}"/>

	</div>

	<div class="form-group">
		<input id="login_password" name="login_password" placeholder="{{ __('user.password') }}"
			   type="password" value="{{ old('login_password') }}"
			   class="form-control{{ (isset($errors) and $errors->login->has('password')) ? ' is-invalid' : '' }}"/>
	</div>

	<div class="form-group form-check">
		<input id="login_remember" name="remember" type="checkbox" class="form-check-input">
		<label class="form-check-label" for="login_remember">{{ __('auth.remember') }}</label>
	</div>

	<button type="submit" class="btn btn-secondary text-nowrap text-truncate btn-block">
		{{ __('auth.enter') }}
	</button>

</form>

<div class="mb-2">
	<a class="btn btn-primary text-nowrap text-truncate btn-block" href="{{ route('invitation') }}">
		{{ __('auth.registration') }}
	</a>
</div>

<div class="mb-2">
	<a class="btn btn-light text-nowrap text-truncate  btn-block" href="{{ route('password.request') }}">
		{{ __('auth.reset_password') }}
	</a>
</div>

<div>

	<a href="{{ route('social_accounts.redirect', ['provider' => 'google']) }}"
	   class="btn btn-sm btn-block d-flex background-color-google-plus color-white">
		<div class="flex-shrink-1 mr-2">
			<i class="fab fa-google-plus-g"></i>
		</div>
		<div class="w-100 text-truncate">
			{{ __('auth.social_account', ['social_network' => 'Google']) }}
		</div>
	</a>

	<a href="{{ route('social_accounts.redirect', ['provider' => 'facebook']) }}"
	   class="btn btn-sm btn-block d-flex background-color-facebook color-white">
		<div class="flex-shrink-1 mr-2">
			<i class="fab fa-facebook-f"></i>
		</div>
		<div class="w-100 text-truncate">
			{{ __('auth.social_account', ['social_network' => 'Facebook']) }}
		</div>
	</a>

	<a href="{{ route('social_accounts.redirect', ['provider' => 'vkontakte']) }}"
	   class="btn btn-sm btn-block d-flex background-color-vk color-white">
		<div class="flex-shrink-1 mr-2">
			<i class="fab fa-vk"></i>
		</div>
		<div class="w-100 text-truncate">
			{{ __('auth.social_account', ['social_network' => 'Vk']) }}
		</div>
	</a>

</div>

<div class="mt-4">
	<a class="btn btn-primary text-nowrap text-truncate  btn-block" href="{{ route('books.create') }}">
		<i class="fas fa-plus"></i> {{ __('Add a book') }}
	</a>
</div>