@extends('layouts.app')

@push('scripts')

@endpush

@section('content')

	@php ($rand = rand(6, 12))

	@if (!empty($votes) and count($votes) > 0)

		@foreach ($votes as $vote)
			@include('user.list.author_book_rate', ['vote' => $vote])
		@endforeach

		@if ($votes->hasPages())
			{{ $votes->appends(request()->except(['page', 'ajax']))->links() }}
		@endif

	@else
		<div class="alert alert-info">
			{{ __('book_vote.nothing_found') }}
		</div>
	@endif

@endsection

