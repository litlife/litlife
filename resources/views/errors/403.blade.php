@extends('layouts.app')

@section('content')

	<div class="row">
		<div class="col-12">
			<div class="alert alert-warning" role="alert">
				<p class="alert-heading">{{ __('common.error') }} 403</p>
				<p>{{ __('error.403') }}</p>
				@if (isset($exception))
					<p class="font-weight-bold">{!! $exception->getMessage() !!}</p>
				@endif
			</div>
		</div>
	</div>

@endsection