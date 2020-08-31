@extends('layouts.app')

@section('content')

	<div class="row">

		<div class="col-md-8 order-md-0 order-1">

			@if ($errors->any())
				<div class="alert alert-danger">
					<ul>
						@foreach ($errors->all() as $error)
							<li>{{ $error }}</li>
						@endforeach
					</ul>
				</div>
			@endif

			@if (session('success'))
				<div class="alert alert-success alert-dismissable">
					{{ session('success') }}
					<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
				</div>
			@endif

			<div class="card mb-3 ">
				<div class="card-body">

					<form action="{{ route('settings.other.update', $user) }}" role="form" method="POST">

						@csrf

						<div class="form-check mb-2">
							<input name="login_with_id" type="hidden" value="0"/>
							<input name="login_with_id" type="checkbox" class="form-check-input" value="1"
								   @if ($user->setting->login_with_id) checked @endif />
							<label class="form-check-label" for="login_with_id">
								{{ __('user_setting.login_with_id') }}
							</label>
						</div>

						<button type="submit" class="btn btn-primary">
							{{ __('common.save') }}
						</button>
					</form>

				</div>
			</div>

		</div>
		<div class="col-md-4 order-md-1 order-0">
			@include ('user.setting.navbar')
		</div>
	</div>

@endsection



