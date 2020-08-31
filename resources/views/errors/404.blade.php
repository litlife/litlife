@extends('layouts.app')

@section('content')

	<div class="alert alert-warning" role="alert">
		<h5 class="alert-heading">{{ __('common.error') }} 404</h5>
		<p>{{ __('error.404') }}</p>
	</div>

	@include('errors.go_previous_page_or_go_back')

@endsection