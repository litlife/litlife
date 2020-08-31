@extends('layouts.app')

@push('scripts')
	<script src="{{ mix('js/authors_list.js', config('litlife.assets_path')) }}"></script>
@endpush

@section('content')

	@include('author.container')

@endsection