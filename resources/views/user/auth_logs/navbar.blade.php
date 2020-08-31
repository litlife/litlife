<ul class="nav nav-pills mb-3">
	<li class="nav-item">
		<a class="nav-link {{ isActiveRoute('users.auth_logs') }}" href="{{ route('users.auth_logs', $user) }}">
			{{ __('user.auth_logs') }}
		</a>
	</li>
	<li class="nav-item">
		<a class="nav-link {{ isActiveRoute('users.auth_fails') }}" href="{{ route('users.auth_fails', $user) }}">
			{{ __('user.auth_fails') }}
		</a>
	</li>
</ul>
