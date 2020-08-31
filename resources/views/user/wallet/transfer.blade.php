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

			<form class="" role="form" action="{{ route('users.wallet.transfer.save', compact('user')) }}"
				  method="post" enctype="multipart/form-data">

				@csrf

				<div class="row form-group">
					<label for="recepient_id" class="col-md-3 col-lg-2 col-form-label">
						{{ __('user_money_transfer.recepient_id') }}
					</label>
					<div class="col-md-9 col-lg-10">
						<input id="recepient_id" name="recepient_id" type="text" aria-describedby="recepientIdHelpInline"
							   class="form-control{{ $errors->has('recepient_id') ? ' is-invalid' : '' }}"
							   value="{{ old('recepient_id') ?? $user->recepient_id }}"/>
					</div>
				</div>

				<div class="row form-group">
					<label for="sum" class="col-md-3 col-lg-2 col-form-label">
						{{ __('user_money_transfer.sum') }}
					</label>
					<div class="col-md-9 col-lg-10">
						<input id="sum" name="sum" type="text" aria-describedby="sumHelpInline"
							   class="form-control{{ $errors->has('sum') ? ' is-invalid' : '' }}"
							   value="{{ old('sum') ?? $user->sum }}"/>
					</div>
				</div>

				<div class="row form-group">
					<div class="col-md-9 col-lg-10 offset-md-3 offset-lg-2">
						<button type="submit" class="btn btn-primary">{{ __('user_money_transfer.transfer_money') }}</button>
					</div>
				</div>

			</form>
		</div>
	</div>

@endsection