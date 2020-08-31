@extends('layouts.app')

@push('scripts')


@endpush

@section('content')

	@include('home.navbar')

	<div class="row mt-3 mb-3">
		<div class="col-12 d-flex">
			<ul class="nav nav-pills">
				<li role="presentation" class="nav-item">
					<a class="nav-link @if ($period == 'day') active @endif"
					   href="{{ route('home.popular_books', ['period' => 'day']) }}">
						{{ __('home.popular_books_range.for_day') }}
					</a>
				</li>
				<li role="presentation" class="nav-item">
					<a class="nav-link @if ($period == 'week') active @endif"
					   href="{{ route('home.popular_books', ['period' => 'week']) }}">
						{{ __('home.popular_books_range.for_week') }}
					</a>
				</li>
				<li role="presentation" class="nav-item">
					<a class="nav-link @if ($period == 'month') active @endif"
					   href="{{ route('home.popular_books', ['period' => 'month']) }}">
						{{ __('home.popular_books_range.for_month') }}
					</a>
				</li>
				<li role="presentation" class="nav-item">
					<a class="nav-link @if ($period == 'quarter') active @endif"
					   href="{{ route('home.popular_books', ['period' => 'quarter']) }}">
						{{ __('home.popular_books_range.for_quarter') }}
					</a>
				</li>
				<li role="presentation" class="nav-item">
					<a class="nav-link @if ($period == 'year') active @endif"
					   href="{{ route('home.popular_books', ['period' => 'year']) }}">
						{{ __('home.popular_books_range.for_year') }}
					</a>
				</li>
			</ul>
		</div>
	</div>

	@if ($books->count())
		<div class="row mt-3">
			@foreach ($books as $book)
				<div class="col-md-6">
					@include('book.list.popular', ['period' => $period])
				</div>
			@endforeach
		</div>
	@else
		<div class="alert alert-info">
			{{ __('home.waiting_for_book_votes') }}
		</div>
	@endif

	@if ($books->hasPages())
		<div class="row mt-3">
			<div class="col-12">
				{{ $books->appends(request()->except(['page', 'ajax']))->links() }}
			</div>
		</div>
	@endif

@endsection