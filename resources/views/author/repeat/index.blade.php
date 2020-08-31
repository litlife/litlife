@extends('layouts.app')

@section('content')

	@can ('create', App\AuthorRepeat::class)
		<div class="row mb-3">
			<div class="col-12">
				<a class="btn btn-primary" href="{{ route('author_repeats.create') }}">
					{{ __('common.add') }}
				</a>
			</div>
		</div>
	@endcan

	@if ($author_repeats->count() > 0)

		@foreach ($author_repeats as $author_repeat)
			@include('author.repeat.item', ['item' => $author_repeat])
		@endforeach


		@if ($author_repeats->hasPages())
			{{ $author_repeats->appends(request()->except(['page', 'ajax']))->links() }}
		@endif

	@else
		<div class="row">
			<div class="col-12">
				<div class="alert alert-info" role="alert">{{ __('author_repeat.nothing_found') }}</div>
			</div>
		</div>
	@endif

@endsection
