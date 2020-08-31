@extends('layouts.app')

@section('content')

	<div class="card">
		<div class="card-body">

			<form action="{{ route('users.emails.store', compact('user')) }}" role="form" method="post"
				  enctype="multipart/form-data">

				@csrf

				@if (count($errors->email) > 0)
					<div class="alert alert-danger">
						<ul>
							@foreach ($errors->email->all() as $error)
								<li>{{ $error }}</li>
							@endforeach
						</ul>
					</div>
				@endif

				<div class="form-group">
					<label for="email" class="col-form-label">
						{{ __('user_email.email') }}
					</label>

					<input class="form-control" name="email" value="{{ old('email') }}"/>

				</div>

				<button type="submit" class="btn btn-primary">{{ __('common.add') }}</button>

			</form>
		</div>
	</div>


@endsection