@extends('layouts.app')

@section('content')

	@if ($requests->hasPages())
		{{ $requests->appends(request()->except(['page', 'ajax']))->links() }}
	@endif


	@if (!$requests->count())
		<div class="alert alert-info">
			{{ __('author_sale_request.nothing_found') }}
		</div>
	@else
		@foreach ($requests as $item)
			@include('author.sales.request.item', ['item' => $item])
		@endforeach
	@endif


	@if ($requests->hasPages())
		{{ $requests->appends(request()->except(['page', 'ajax']))->links() }}

	@endif


@endsection