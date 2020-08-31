@extends('layouts.app')

@push('scripts')
	<script src="{{ mix('js/collections.index.js', config('litlife.assets_path')) }}"></script>
@endpush

@section('content')

	@include('collection.search')

@endsection