@component('components.comment', get_defined_vars())

	@slot('avatar')
		<x-user-avatar :user="$item->create_user" width="50" height="50"/>
	@endslot

	@slot('data_attributes')
		data-id="{{ $item->id }}"
	@endslot

	<h6 class="mb-3">
		<x-user-name :user="$item->create_user"/>
	</h6>

	<div class="mb-3">
		{{ $item->text  }}
	</div>

	<div class="status">
		@include('support_request.alert')
	</div>

	<div class="">
		<a class="btn btn-outline-primary" href="{{ route('support_requests.show', $item) }}">
			{{ __('Просмотреть запрос') }}
		</a>

		<a class="btn-start-review btn btn-outline-success" href="{{ route('support_requests.start_review', $item) }}"
		   @cannot ('startReview', $item) style="display:none;" @endcannot>
			{{ __('Start reviewing') }}
		</a>

		<a class="btn-approve btn btn-outline-success" href="{{ route('support_requests.approve', $item) }}"
		   @cannot ('approve', $item) style="display:none;" @endcannot>
			{{ __('Request reviewed') }}
		</a>

		<a class="btn-decline btn btn-outline-secondary" href="{{ route('support_requests.decline', $item) }}"
		   @cannot ('decline', $item) style="display:none;" @endcannot>
			{{ __('Decline to review') }}
		</a>

		<a class="btn-stop-review btn btn-outline-success"
		   href="{{ route('support_requests.stop_review', $item) }}"
		   @cannot ('stopReview', $item) style="display:none;" @endcannot>
			{{ __('Decline to review') }}
		</a>
	</div>

	@slot('descendants')

	@endslot

@endcomponent