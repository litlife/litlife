@extends('layouts.app')

@push('scripts')

@endpush

@section('content')

	@if ($errors->any())
		<div class="alert alert-danger">
			<ul>
				@foreach ($errors->all() as $error)
					<li>{{ $error }}</li>
				@endforeach
			</ul>
		</div>
	@endif

	@if (session('success'))
		<div class="alert alert-success alert-dismissable">
			{{ session('success') }}
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		</div>
	@endif

	@can ('create', \App\SupportRequest::class)
		<div class="mb-3">
			<a href="{{ route('support_requests.create', ['user' => $user]) }}" class="btn btn-primary">
				{{ __('New support request') }}
			</a>
		</div>
	@endcan

	@if ($supportRequests->hasPages())
		{{ $supportRequests->appends(request()->except(['page', 'ajax']))->links() }}
	@endif

	@if (!$supportRequests->count())
		<div class="alert alert-info">
			{{ __('No support requests were found') }}
		</div>
	@else
		<div class="list-group">
			@foreach ($supportRequests as $item)
				@include('user.support_request.item', ['item' => $item])
			@endforeach
		</div>
	@endif

	@if ($supportRequests->hasPages())
		{{ $supportRequests->appends(request()->except(['page', 'ajax']))->links() }}
	@endif

@endsection