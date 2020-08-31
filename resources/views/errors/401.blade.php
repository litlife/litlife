@extends('layouts.app')

@section('content')

	<div class="alert alert-warning" role="alert">
		<h6>{{ __('error.401') }}</h6>
		@if (isset($exception))
			<p>{!! $exception->getMessage() !!}</p>
		@endif
	</div>

	@include('errors.go_previous_page_or_go_back')

@endsection