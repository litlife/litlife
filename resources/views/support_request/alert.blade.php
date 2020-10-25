@if ($item->isAccepted())
	<div class="alert alert-success alert-accepted" role="alert">
		{{ __('The issue has been resolved') }}.

		@if (!empty($item->status_changed_user))
			{{ __('manager.checked_by_user') }}
			<x-user-name :user="$item->status_changed_user"/>
		@endif

		<x-time :time="$item->status_changed_at ?? null"/>
	</div>
@endif

@if ($item->isReviewStarts())
	<div class="alert alert-info alert-rejected" role="alert">
		{{ __('The request is reviewed by') }}

		@if (!empty($item->status_changed_user))
			<x-user-name :user="$item->status_changed_user"/>
		@endif

		<x-time :time="$item->status_changed_at ?? null"/>
	</div>
@endif
