@extends('layouts.app')

@push('scripts')

@endpush

@section('content')

	<div class="card">
		<div class="card-body">

			@section('cover')
				<div class="text-center">
					<x-book-cover :book="$book" width="600" height="600" href="0" style="max-width: 100%;"/>
				</div>
			@show

		</div>
	</div>

@endsection
