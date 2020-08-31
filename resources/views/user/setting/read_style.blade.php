@extends('layouts.app')

@push('scripts')

	<script src="{{ mix('js/users.settings.read_style.js', config('litlife.assets_path')) }}"></script>

@endpush

@push('css')

	<link href="{{ mix('css/bootstrap-colorpicker.css', config('litlife.assets_path')) }}" rel="stylesheet">

@endpush

@section('content')

	<div class="row">
		<div class="col-md-8 order-md-0 order-1">

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

			<div class="card mb-3 ">
				<div class="card-body">
					@include('user.setting.read_style_form')
				</div>
			</div>
		</div>
		<div class="col-md-4  order-md-1 order-0">

			@include ('user.setting.navbar')

		</div>
	</div>


@endsection



