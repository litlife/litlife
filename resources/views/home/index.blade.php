@extends('layouts.app')

@push('scripts')
	<script src="{{ mix('js/books_list.js', config('litlife.assets_path')) }}"></script>
@endpush

@section('content')

	@include('home.navbar')

	@if ($books->currentPage() < 2)

		@include('text_block/item', ['name' => 'MainPage'])

	@endif

	@include('book.container')

@endsection