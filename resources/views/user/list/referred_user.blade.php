@component('user.list.default', ['user' => $user])
	<small class="card-text mt-2">
		{{ __('referred_users.percent_of_your_purchase') }}: {{ $user->pivot->comission_buy_book }}% <br/>
		{{ __('referred_users.percentage_of_sales') }}: {{ $user->pivot->comission_sell_book }}% <br/>
		{{ __('referred_users.referred_at') }}:
		<x-time :time="$user->pivot->created_at"/>
	</small>
@endcomponent