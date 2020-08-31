@extends('layouts.app')

@push('css')

@endpush

@push('scripts')

@endpush

@section('content')

	@if (count($errors) > 0)
		<div class="alert alert-danger">
			<ul>
				@foreach ($errors->all() as $error)
					<li>{{ $error }}</li>
				@endforeach
			</ul>
		</div>
	@endif

	@if (session('success'))
		<div class="alert alert-success">
			{{ session('success') }}
		</div>
	@endif

	<div class="card mb-2">
		<div class="card-body">

			<form class="" role="form" action="{{ route('users.wallet.withdrawal', compact('user')) }}"
				  method="post" enctype="multipart/form-data">

				@csrf

				<fieldset class="form-group">
					<div class="row">
						<legend
								class="col-md-3 col-lg-2 col-form-label pt-0">{{ __('user_outgoing_payment.details') }}</legend>
						<div class="col-md-9 col-lg-10">
							@if (count($user->getFilledWallets()) < 1)

								{{ __('user_outgoing_payment.to_withdraw_you_must_add_your_billing_information') }}

								<br/>

								<a class="btn btn-primary"
								   href="{{ route('users.wallet.payment_details', $user) }}">{{ __('user_outgoing_payment.add_wallet') }}</a>
							@else
								@foreach ($user->getFilledWallets() as $wallet)
									<div class="form-check">
										<input class="form-check-input {{ $errors->has('wallet') ? ' is-invalid' : '' }}"
											   type="radio" name="wallet"
											   id="gridRadios{{ $wallet->type }}"
											   @if ($wallet->id == old('wallet')) checked="checked" @endif
											   value="{{ $wallet->id }}">
										<label class="form-check-label" for="gridRadios{{ $wallet->type }}">
											<strong>{{ __('user_payment_detail.'.$wallet->type) }}</strong>
											@if ($wallet->isCard())
												{{ $wallet->getCountryCode() }} {{ $wallet->getCardBrand() }}
											@endif
											{{ $wallet->number }} <br/>

											@if ($wallet->getComission())
												Комиссия {{ $wallet->getComission() }} % <br/>
											@endif

											@if ($wallet->getMinComissionSum())
												Минимальная комиссия {{ $wallet->getMinComissionSum() }} р. <br/>
											@endif

											@if ($wallet->getMin() > config('litlife.min_outgoing_payment_sum'))
												@if ($wallet->getMin())
													Минимальная сумма {{ $wallet->getMin() }} р. <br/>
												@endif
											@endif

											@if ($wallet->getMax())
												Максимальная сумма {{ $wallet->getMax() }} р. <br/>
											@endif

											@if ($wallet->getMaxInMonth())
												Максимально в месяц {{ $wallet->getMaxInMonth() }} р. <br/>
											@endif

											@if ($wallet->getMaxInDay())
												Максимально в день {{ $wallet->getMaxInDay() }} р. <br/>
											@endif
										</label>
									</div>
								@endforeach

								<small id="paymentDetailsHelp"
									   class="form-text text-muted">{{ __('user_outgoing_payment.select_billing_information') }}</small>

								<a class="btn btn-outline-primary btn-sm mt-2"
								   href="{{ route('users.wallet.payment_details', $user) }}">{{ __('user_outgoing_payment.edit_billing_information') }}</a>
							@endif
						</div>
					</div>
				</fieldset>


				<div class="row form-group">
					<label for="sum" class="col-md-3 col-lg-2 col-form-label">
						{{ __('user_outgoing_payment.sum') }}
					</label>
					<div class="col-md-9 col-lg-10">
						<input id="sum" name="sum" type="text" aria-describedby="sumHelpInline"
							   class="form-control{{ $errors->has('sum') ? ' is-invalid' : '' }}"
							   value="{{ old('sum') ?? $user->sum }}"/>
						<small id="sumHelpInline" class="text-muted">
							{{ __('user_outgoing_payment.minimum_withdrawal_sum', ['sum' => config('litlife.min_outgoing_payment_sum')]) }}
						</small>
					</div>
				</div>

				<div class="row form-group">
					<div class="col-md-9 col-lg-10 offset-md-3 offset-lg-2">
						<button type="submit" class="btn btn-primary">{{ __('user_outgoing_payment.order_payment') }}</button>
					</div>
				</div>

				{{ __('common.comissions') }}:

				<ul>
					<li>{{ __('user_payment_transaction.payment_types_array.card_rf') }} {{ __('user_payment_transaction.withdrawal_comissions_description.card_rf') }}</li>
					<li>{{ __('user_payment_transaction.payment_types_array.card_not_rf') }} {{ __('user_payment_transaction.withdrawal_comissions_description.card_not_rf') }}</li>
					<li>{{ __('user_payment_transaction.payment_types_array.webmoney') }} {{ __('user_payment_transaction.withdrawal_comissions_description.webmoney') }}</li>
					<li>{{ __('user_payment_transaction.payment_types_array.yandex') }} {{ __('user_payment_transaction.withdrawal_comissions_description.yandex') }}</li>
					<li>{{ __('user_payment_transaction.payment_types_array.qiwi') }} {{ __('user_payment_transaction.withdrawal_comissions_description.qiwi') }}</li>
				</ul>

			</form>
		</div>
	</div>

@endsection