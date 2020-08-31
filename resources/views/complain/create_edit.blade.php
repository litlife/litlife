@extends('layouts.app')

@section('content')

	@if (session('success'))
		<div class="alert alert-success alert-dismissable">
			{{ session('success') }}
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		</div>
	@endif

	@if ($errors->any())
		<div class="alert alert-danger">
			<ul>
				@foreach ($errors->all() as $error)
					<li>{{ $error }}</li>
				@endforeach
			</ul>
		</div>
	@endif

	<div class="card">
		<div class="card-body">

			<form role="form" method="POST" action="{{ route('complains.save', compact('type', 'id')) }}"
				  enctype="multipart/form-data">
				@csrf


				<div class="form-group">
					<label for="text" class="col-form-label">
						{{ __('complain.text') }}
					</label>
					<textarea id="text" class="form-control" rows="5" name="text">{{ @$complain->text }}</textarea>

				</div>

				<button type="submit" class="btn btn-primary">
					@if (empty($complain))
						{{ __('common.send') }}
					@else
						{{ __('common.save') }}
					@endif
				</button>

			</form>
		</div>
	</div>
@endsection