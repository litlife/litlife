@extends('layouts.app')

@push('scripts')


@endpush

@section('content')

	<div class="row">
		<div class="col-12 text-center">
			<h4>{{ $award->title }}</h4>
		</div>
	</div>

	<div class="row">
		<div class="col-12 text-center">
			<p>{{ $award->description }}</p>
		</div>
	</div>


@endsection