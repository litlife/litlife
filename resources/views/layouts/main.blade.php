@extends('layouts.app')

@section('content')

	<div class="container">
		<div class="row">
			<div class="col-md-8">

				<div class="panel panel-default">

					<div class="panel-body">

						@yield('content')

					</div>
				</div>
			</div>
		</div>
	</div>
@endsection