@extends('layouts.app')

@push('scripts')

@endpush

@push('css')

@endpush

@section('content')

	<div class="alert alert-warning" role="alert">
		<h5 class="alert-heading">{{ __('common.error') }} 404</h5>
		<p>{{ __('section.book_page_was_not_found') }}</p>
	</div>

	<div class="text-center">
		<a class="btn btn-primary" href="{{ route('books.sections.index', ['book' => $book]) }}">
			{{ __('section.go_to_the_sections_index') }}
		</a>
	</div>

@endsection