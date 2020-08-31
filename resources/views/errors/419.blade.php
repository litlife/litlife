@extends('layouts.app')

@section('content')

	<div class="alert alert-warning" role="alert">
		<h5 class="alert-heading">{{ __('common.error') }} 419</h5>
		<p>{{ __('error.419') }}</p>
		@if (isset($exception))
			<p>{!! $exception->getMessage() !!}</p>
		@endif
	</div>

@endsection