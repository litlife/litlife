@extends('layouts.app')

@push('scripts')

@endpush

@section('content')


	<a class="btn btn-primary" href="{{ route('books.make_accepted', $book) }}">{{ __('book.make_accepted') }}</a>

	@include('text_block.item', ['name' => 'Правила добавления книг'])


@endsection
