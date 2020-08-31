@extends('layouts.app')

@push('scripts')

@endpush

@section('content')

	<div class="card">
		<div class="card-body text-center">
			<x-author-photo :author="$author" width="600" height="600" style="max-width: 100%;"/>
		</div>
	</div>

@endsection
