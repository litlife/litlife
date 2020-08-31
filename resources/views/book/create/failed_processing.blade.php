@extends('layouts.app')

@push('scripts')

@endpush

@section('content')

	@include ('book.create.tab')

	<div class="alert alert-danger" role="alert">
		{{ __('book.parse.failed') }}
	</div>

	<div class="alert alert-info">
		{{ __('Try downloading the book in a different format or try downloading this book later') }}
	</div>

	@can('retry_failed_parse', $book)
		<a href="{{ route('books.retry_failed_parse', $book) }}"
		   class="btn btn-primary mb-3">{{ __('book.retry_failed_parse') }}</a>
	@endcan

	@can('delete', $book)
		<a href="{{ route('books.delete', $book) }}"
		   class="btn btn-primary mb-3">{{ __('book.delete_a_book') }}</a>
	@endcan

@endsection
