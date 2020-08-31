@extends('layouts.without_sidebar_and_footer')

@section('content')

	<div class="container">
		<div class="row justify-content-center">
			<div class="col-md-4">
				<div class="card">
					<div class="card-header">{{ __('auth.enter_the_site') }}</div>

					<div class="card-body">
						@include('auth.form')
					</div>
				</div>
			</div>
		</div>
	</div>

@endsection
