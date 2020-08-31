@extends('layouts.app')

@push('scripts')
	<script src="{{ mix('js/collections.show.js', config('litlife.assets_path')) }}"></script>
@endpush

@section('content')

	@include('collection.show_navbar')

	<div class="collection">
		@include('collection.item', ['item' => $collection])
	</div>


@endsection
