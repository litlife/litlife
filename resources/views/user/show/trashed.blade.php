@extends('user.show.layout')

@section ('avatar')
	<div class="row mb-3">
		<div class="col-12 text-center mb-3">

			<x-user-avatar :user="null" width="180" height="300"
						   class="img-fluid rounded avatar pointer lazyload"
						   href="0"
						   style="max-width: 100%;"/>

		</div>
	</div>
@endsection
@section ('relations') @endsection
@section ('achievements') @endsection
@section ('admin_note') @endsection
@section ('description')@endsection
@section ('blog') @endsection