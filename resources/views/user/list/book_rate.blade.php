@component('user.list.default', ['user' => $user, 'rand' => $rand ?? ''])

	<p class="card-text mb-2">
		{{ __('common.vote') }}:
		<x-book-vote :vote="$user->pivot->vote"/>

		@if (!empty($user->pivot->user_updated_at))
			<small>
				<x-time :time="$user->pivot->user_updated_at"/>
			</small>
		@endif
	</p>

@endcomponent