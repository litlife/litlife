@extends('layouts.app')

@push('scripts')

@endpush

@push('css')

@endpush

@section('content')

	@can('createMessage', $supportRequest)
		<div class="card mb-2">
			<div class="card-body">
				@include('support_request.message.form')
			</div>
		</div>
	@endcan

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