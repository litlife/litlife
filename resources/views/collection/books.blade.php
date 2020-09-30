@extends('layouts.app')

@push('scripts')
	<script src="{{ mix('js/collections.books.js', config('litlife.assets_path')) }}"></script>
@endpush

@section ('content')

	@include('collection.show_navbar')

	@can('addBook', $collection)
		<div class="mb-3">
			<a href="{{ route('collections.books.select', $collection) }}" target="_blank"
			   class="btn btn-primary">{{ __('collection.attach_book') }}</a>
		</div>
	@endcan

	@include('book.container')

@endsection

