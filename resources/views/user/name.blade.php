@if (isset($user))
	@if ($user->trashed())
		{{ __('user.deleted') }}
	@else
		@if (empty($href_disable))
			<a href="{{ route('profile', $user) }}">
				@endif

				<span style="color: #E14900"
					  @if (!empty($itemprop)) itemprop="{{ $itemprop }}"
					  @endif @if ($user->isOnline()) class="online" @endif>
                        {{ $user->userName }}
                    </span>

				@if (empty($href_disable))
			</a>
		@endif
	@endif
@else
	{{ __('user.deleted') }}
@endif

