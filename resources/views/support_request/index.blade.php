@extends('layouts.app')

@push('scripts')

@endpush

@section('content')

	@if ($supportRequests->hasPages())
		{{ $supportRequests->appends(request()->except(['page', 'ajax']))->links() }}
	@endif

	@if (!$supportRequests->count())
		<div class="alert alert-info">
			{{ __('No support requests were found') }}
		</div>
	@else
		<div>
			@foreach ($supportRequests as $item)
				@include('support_request.item', ['item' => $item])
			@endforeach
		</div>
	@endif

	@if ($supportRequests->hasPages())
		{{ $supportRequests->appends(request()->except(['page', 'ajax']))->links() }}
	@endif

@endsection