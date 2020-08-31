@extends('layouts.app')

@push('scripts')

@endpush

@section('content')


	<a class="btn btn-primary" href="{{ route('books.add_for_review', $book) }}">{{ __('book.add_for_review') }}</a>

	@include('text_block.item', ['name' => 'Правила добавления книг'])


@endsection
