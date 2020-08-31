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
		<div class="alert alert-success">{{ session('success') }}</div>
	@endif

	<div class="card mb-2">
		<div class="card-body">

			<form role="form" action="{{ route('users.wallet.payment_details.save', compact('user')) }}"
				  method="post" enctype="multipart/form-data">

				@csrf

				<div class="row form-group">
					<label for="card" class="col-md-3 col-lg-2 col-form-label">{{ __('user_payment_detail.card') }}</label>
					<div class="col-md-9 col-lg-10">
						<input id="card" name="card" type="text"
							   class="form-control{{ $errors->has('card') ? ' is-invalid' : '' }}"
							   value="{{ old('card') ?? optional($user->wallets->where('type', 'card')->first())->number }}"/>
					</div>
				</div>

				<div class="row form-group">
					<label for="qiwi" class="col-md-3 col-lg-2 col-form-label">{{ __('user_payment_detail.qiwi') }}</label>
					<div class="col-md-9 col-lg-10">
						<input id="qiwi" name="qiwi" type="text"
							   class="form-control{{ $errors->has('qiwi') ? ' is-invalid' : '' }}"
							   value="{{ old('qiwi') ?? optional($user->wallets->where('type', 'qiwi')->first())->number }}"/>
					</div>
				</div>

				<div class="row form-group">
					<label for="wmr" class="col-md-3 col-lg-2 col-form-label">{{ __('user_payment_detail.wmr') }}</label>
					<div class="col-md-9 col-lg-10">
						<input id="wmr" name="wmr" type="text"
							   class="form-control{{ $errors->has('wmr') ? ' is-invalid' : '' }}"
							   value="{{ old('wmr') ?? optional($user->wallets->where('type', 'wmr')->first())->number }}"/>
					</div>
				</div>

				<div class="row form-group">
					<label for="yandex"
						   class="col-md-3 col-lg-2 col-form-label">{{ __('user_payment_detail.yandex') }}</label>
					<div class="col-md-9 col-lg-10">
						<input id="yandex" name="yandex" type="text"
							   class="form-control{{ $errors->has('yandex') ? ' is-invalid' : '' }}"
							   value="{{ old('yandex') ?? optional($user->wallets->where('type', 'yandex')->first())->number }}"/>
					</div>
				</div>

				<button type="submit" class="btn btn-primary">{{ __('common.save') }}</button>

			</form>

		</div>
	</div>

@endsection