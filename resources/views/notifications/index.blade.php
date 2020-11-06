@extends('layouts.app')

@push('scripts')


@endpush

@section('content')

	@if ($notifications->count() < 1)
		<div class="alert alert-info" role="alert">
			{{ __('notification.nothing_found') }}
		</div>
	@else

		<div class="list-group mb-3">
			@foreach ($notifications as $notification)
				<a href="{{ $notification->data['url'] ?? '' }}"
				   class="list-group-item list-group-item-action @if(!empty($notification->read_at)) list-group-item-light @else font-weight-bold @endif">

					<div class="d-flex w-100 justify-content-between">
						<h6 class="mb-1">
							@if (!empty($notification->data['title']))
								{{ $notification->data['title'] ?? '' }}
							@endif
						</h6>
						<small class="text-right text-nowrap d-none d-sm-block">
							<x-time :time="$notification->created_at"/>
						</small>
					</div>

					@if (!empty($notification->data['description']))
						<p class="mb-1">{{ $notification->data['description'] ?? '' }}</p>
					@endif

					<small class="text-nowrap d-block d-sm-none">
						<x-time :time="$notification->created_at"/>
					</small>

				</a>
			@endforeach
		</div>

		@if ($notifications->hasPages())
			{{ $notifications->appends(request()->except(['page', 'ajax']))->links() }}
		@endif

	@endif

@endsection
