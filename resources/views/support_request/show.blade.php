@extends('layouts.app')

@push('scripts')

@endpush

@push('css')

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

	@can('createMessage', $supportRequest)
		<div class="card mb-2">
			<div class="card-body">
				@include('support_request.message.form')
			</div>
		</div>
	@endcan

	@if ($supportRequest->isAuthUserCreator())
		@can ('solve', $supportRequest)
			<div class="alert alert-info mb-2">
				{{ __('Is your question resolved? If Yes, please click here:') }}
				<a href="{{ route('support_requests.solve', $supportRequest) }}" class="alert-link">
					{{ __('My question is resolved') }}
				</a>
			</div>
		@endcan
	@endif

	@if(!empty($messages) and count($messages) > 0)

		@if ($messages->hasPages())
			<div class="row mt-3">
				<div class="col-12">
					{{ $messages->appends(request()->except(['page', 'ajax']))->links() }}
				</div>
			</div>
		@endif

		@foreach ($messages as $item)

			@include('support_request.message.item')

		@endforeach

		@if ($messages->hasPages())
			<div class="row mt-3">
				<div class="col-12">
					{{ $messages->appends(request()->except(['page', 'ajax']))->links() }}
				</div>
			</div>
		@endif

	@else
		<div class="row mt-3">
			<div class="col-12">
				<div class="alert alert-info">{{ __('No messages found') }}</div>
			</div>
		</div>
	@endif

@endsection