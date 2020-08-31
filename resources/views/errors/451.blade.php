@extends('layouts.without_sidebar')

@section('content')

	<div class="row">
		<div class="col-12">

			<div class="alert alert-info" role="alert">
				<h5 class="alert-warning">{{ __('common.error') }} 451</h5>
				<p>{{ __('error.451') }}</p>
				@if (isset($exception))
					<p>{!! $exception->getMessage() !!}</p>
				@endif
			</div>
		</div>
	</div>

@endsection