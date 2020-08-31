@component('user.list.default', ['user' => $user, 'rand' => $rand ?? ''])
	<p class="card-text mt-1">
		<x-time :time="\Carbon\Carbon::parse($user->likes_created_at)"/>
	</p>
@endcomponent