@extends('layouts.app')

@section('content')

	<div class="row">
		<div class="col-12">
			<div class="alert alert-warning" role="alert">
				<p class="alert-heading">{{ __('common.error') }} 500</p>
				<p>{{ __('error.500') }}</p>
				<p>{{ __('error.description.500') }}</p>
				<p><a class="btn btn-primary" href="{{ \Request::url() }}">{{ __('error.reload_page') }}</a></p>
			</div>
		</div>
	</div>

@endsection