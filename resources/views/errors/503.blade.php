@extends('layouts.without_sidebar_and_footer')

@section('content')

	<div class="alert alert-warning" role="alert">
		<p>{{ __('error.503') }}</p>
		<p>{{ __('common.please_come_back_a_little_later') }}</p>
		@if (isset($exception))
			<p>{!! $exception->getMessage() !!}</p>
		@endif
	</div>

@endsection