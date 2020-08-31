<div class="card mb-3">
	<ul class="nav nav-pills flex-column" aria-orientation="vertical">
		<li class="nav-item">
			<a class="nav-link {{ isActiveRoute('allowance') }}"
			   href="{{ route('allowance', $user) }}">
				{{ __('user.allowance') }}
			</a>
		</li>
		<li class="nav-item">
			<a class="nav-link {{ isActiveRoute('users.settings.notifications') }}"
			   href="{{ route('users.settings.notifications', $user) }}">
				{{ __('user.email_delivery') }}
			</a>
		</li>
		<li class="nav-item">
			<a class="nav-link {{ isActiveRoute('users.emails.index') }}" href="{{ route('users.emails.index', $user) }}">
				{{ __('user.emails') }}
			</a>
		</li>
		<li class="nav-item">
			<a class="nav-link {{ isActiveRoute('users.social_accounts.index') }}"
			   href="{{ route('users.social_accounts.index', $user) }}">
				{{ __('user.social_accounts') }}
			</a>
		</li>
		<li class="nav-item">
			<a class="nav-link {{ isActiveRoute('users.settings.read_style') }}"
			   href="{{ route('users.settings.read_style', $user) }}">
				{{ __('user.read_style') }}
			</a>
		</li>
		<li class="nav-item">
			<a class="nav-link {{ isActiveRoute('genre_blacklist') }}"
			   href="{{ route('genre_blacklist', $user) }}">
				{{ __('user.genre_blacklist') }}
			</a>
		</li>
		<li class="nav-item">
			<a class="nav-link {{ isActiveRoute('users.settings.site_appearance') }}"
			   href="{{ route('users.settings.site_appearance', $user) }}">
				{{ __('user_setting.site_appearance') }}
			</a>
		</li>
		<li class="nav-item">
			<a class="nav-link {{ isActiveRoute('settings.other') }}" href="{{ route('settings.other', $user) }}">
				{{ __('user.other_settings') }}
			</a>
		</li>
		<li class="nav-item">
			<a class="nav-link" href="{{ route('users.blacklists', $user) }}">
				{{ __('user.blacklist_users') }}
			</a>
		</li>
	</ul>
</div>