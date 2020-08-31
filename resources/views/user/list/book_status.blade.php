@component('user.list.default', ['user' => $user, 'rand' => $rand ?? ''])

	<p class="card-text mb-2">
		@if (!empty($user->pivot->user_updated_at))
			<small>
				{{ __('book.read_status_date') }}:
				<x-time :time="\Carbon\Carbon::parse($user->pivot->user_updated_at)"/>
			</small>
		@endif
	</p>

@endcomponent