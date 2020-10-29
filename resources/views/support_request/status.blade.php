@if ($item->isAccepted())
	<div class="alert alert-success alert-accepted mt-3 mb-0" role="alert">
		{{ __('Request has been resolved') }}.

		@if (!empty($item->status_changed_user))
			{{ __('manager.checked_by_user') }}
			<x-user-name :user="$item->status_changed_user"/>
		@endif

		<x-time :time="$item->status_changed_at ?? null"/>
	</div>
@endif

@if ($item->isReviewStarts())
	<div class="alert alert-info alert-rejected mt-3 mb-0" role="alert">
		{{ __('Request is reviewed by') }}

		@if (!empty($item->status_changed_user))
			<x-user-name :user="$item->status_changed_user"/>
		@endif

		<x-time :time="$item->status_changed_at ?? null"/>
	</div>
@endif
