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
			<form class="" role="form" action="{{ route('users.wallet.deposit.pay', compact('user')) }}"
				  method="post" enctype="multipart/form-data">

				@csrf

				<div class="row form-group">
					<label for="sum" class="col-md-3 col-lg-2 col-form-label">
						{{ __('user_incoming_payment.sum') }}
					</label>
					<div class="col-md-9 col-lg-10">
						<input id="sum" name="sum" type="text" aria-describedby="sumHelpInline"
							   class="form-control{{ $errors->has('sum') ? ' is-invalid' : '' }}"
							   value="{{ old('sum') ?? '500' }}"/>
						<small id="sumHelpInline" class="text-muted">
							{{ __('user_incoming_payment.sum_helper') }}
						</small>
					</div>
				</div>

				<fieldset class="form-group">
					<div class="row">
						<legend class="col-form-label col-sm-2 pt-0">{{ __('user_incoming_payment.type') }}</legend>
						<div class="col-sm-10">
							@foreach (config('unitpay.allowed_payment_types') as $type)

								@if ($type == 'mc')
									@foreach (config('unitpay.allowed_mobile_payment_types') as $type)
										<div class="form-check">
											<input class="form-check-input" type="radio" name="payment_type"
												   id="gridRadios{{ $type }}" value="mc">
											<label class="form-check-label" for="gridRadios{{ $type }}">
												{{ __('user_payment_transaction.payment_types_array.'.$type) }}

												~{{ config('unitpay.deposit_comissions.'.$type) }}%
											</label>
										</div>
									@endforeach
								@else
									<div class="form-check">
										<input class="form-check-input" type="radio" name="payment_type"
											   id="gridRadios{{ $type }}" value="{{ $type }}">
										<label class="form-check-label" for="gridRadios{{ $type }}">
											{{ __('user_payment_transaction.payment_types_array.'.$type) }}

											~{{ config('unitpay.deposit_comissions.'.$type) }}%
										</label>
									</div>
								@endif

							@endforeach
						</div>
					</div>
				</fieldset>


				<div class="row form-group">
					<div class="col-md-9 col-lg-10 offset-md-3 offset-lg-2">
						<button type="submit" class="btn btn-primary">{{ __('user_payment_transaction.go_to_pay') }}</button>
					</div>
				</div>

			</form>
		</div>
	</div>

@endsection