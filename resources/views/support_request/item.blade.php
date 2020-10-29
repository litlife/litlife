<div class="card mb-2 support_request">
	<div class="card-header d-flex">
		<div class="flex-grow-1 d-flex flex-row align-items-center">
			<div class="mr-2" style="min-width: 30px; max-width: 30px;">
				<x-user-avatar :user="$item->create_user" width="30" height="30"/>
			</div>
			<x-user-name :user="$item->create_user"/>
		</div>
		<div class="flex-shrink-1 small text-nowrap d-flex flex-row align-items-center">
			{{ __('Messages') }}:
			{{ $item->number_of_messages }}
		</div>
	</div>
	<div class="card-body">

		<h6 class="card-title">
			<a href="{{ route('support_requests.show', $item) }}">
				{{ $item->title }}
			</a>
		</h6>

		<hr/>

		@isset($item->latest_message)

			<div class="mb-0">
				<div class="d-flex flex-row">
					<div class="mr-2">
						<x-user-avatar :user="$item->latest_message->create_user" width="50" height="50"/>
					</div>
					<div class="d-flex flex-column">
						<div class="mb-2">
							<x-user-name :user="$item->latest_message->create_user"/>

							<small>
								<x-time :time="$item->latest_message->created_at"/>
							</small>
						</div>
						<div>
							{{ \Illuminate\Support\Str::limit($item->latest_message->text, 100) }}
						</div>
					</div>
				</div>
			</div>

		@else

			<div>{{ __('Last message not found') }}</div>

		@endisset

		<div class="status">
			@include('support_request.status')
		</div>
	</div>

	<div class="card-footer buttons">

		<a class="btn-continue-reviewing btn btn-primary" href="{{ route('support_requests.show', $item) }}"
		   @cannot ('continueReviewing', $item) style="display:none;" @endcannot>
			{{ __('Сontinue reviewing') }}
		</a>

		<a class="btn-start-review btn btn-primary" href="{{ route('support_requests.start_review', $item) }}"
		   @cannot ('startReview', $item) style="display:none;" @endcannot>
			{{ __('Start reviewing') }}
		</a>

		<a class="btn btn-approve btn-outline-success" href="{{ route('support_requests.solve', $item) }}"
		   @cannot ('solve', $item) style="display:none;" @endcannot>
			<i class="far fa-check-circle"></i> {{ __('Mark as resolved') }}
		</a>

		<a class="btn-stop-review btn btn-outline-danger"
		   href="{{ route('support_requests.stop_review', $item) }}"
		   @cannot ('stopReview', $item) style="display:none;" @endcannot>
			<i class="far fa-times-circle"></i> {{ __('Decline to review') }}
		</a>

	</div>
</div>