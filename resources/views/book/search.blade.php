@extends('layouts.app')

@push('scripts')
	<script src="{{ mix('js/books_list.js', config('litlife.assets_path')) }}"></script>
@endpush

@section('content')

	@include('book.container')

@endsection