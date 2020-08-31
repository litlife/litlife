@extends('layouts.app')

@push('scripts')

@endpush

@section('content')
	<div class="row">
		<div class="col-12">
			<div class="alert alert-info" role="alert">
				{{ __('user.unauthorized_error') }}
			</div>

		</div>
	</div>

@endsection