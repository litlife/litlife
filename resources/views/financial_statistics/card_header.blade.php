<div class="card-header">
	<ul class="nav nav-tabs card-header-tabs">
		<li class="nav-item">
			<a class="nav-link {{ active('financial_statistic.index') }}"
			   href="{{ route('financial_statistic.index') }}">{{ __('user_payment_transaction.history') }}</a>
		</li>
		<li class="nav-item">
			<a class="nav-link {{ active('financial_statistic.all_transactions') }}"
			   href="{{ route('financial_statistic.all_transactions') }}">Все транзакции</a>
		</li>

		<li class="nav-item">
			<a class="nav-link {{ active('financial_statistic.purchases') }}"
			   href="{{ route('financial_statistic.purchases') }}">Все продажи</a>
		</li>
	</ul>
</div>