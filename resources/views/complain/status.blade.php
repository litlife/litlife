@if ($item->isAccepted())
	<div class="alert alert-success alert-accepted" role="alert">
		<i class="far fa-check-circle"></i> {{ __('complain.complaint_was_reviewed') }}

		@if (!empty($item->status_changed_user))
			<x-user-name :user="$item->status_changed_user"/>
		@endif

		<x-time :time="$item->status_changed_at ?? null"/>
	</div>
@endif

@if ($item->isReviewStarts())
	<div class="alert alert-info alert-rejected" role="alert">
		<i class="far fa-clock"></i> {{ __('complain.review_by_user') }}

		@if (!empty($item->status_changed_user))
			<x-user-name :user="$item->status_changed_user"/>
		@endif

		<x-time :time="$item->status_changed_at ?? null"/>
	</div>
@endif