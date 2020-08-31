@extends('layouts.app')

@section('content')

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

	<div class="card">
		<div class="card-body">

			<form role="form" method="POST" action="{{ route('mailings.store') }}"
				  enctype="multipart/form-data">
				@csrf

				<div class="form-group">
					<div>
                  <textarea id="text" name="text" style="height: 500px;"
							class="form-control{{ $errors->has('text') ? ' is-invalid' : '' }}"
							type="text">{{ old('text') }}</textarea>
					</div>
				</div>

				<button type="submit" class="btn btn-primary">{{ __('common.add') }}</button>

			</form>

		</div>
	</div>

@endsection