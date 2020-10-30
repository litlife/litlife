@if ($item->isAccepted())
	<div class="alert alert-success alert-accepted mt-3 mb-0" role="alert">
		<x-user-name :user="$item->status_changed_user"/>
		{{ __('marked the issue as resolved') }}
		<x-time :time="$item->status_changed_at ?? null"/>
	</div>
@endif

@if ($item->isReviewStarts())
	<div class="alert alert-info alert-rejected mt-3 mb-0" role="alert">
		<x-user-name :user="$item->status_changed_user"/>
		{{ __('is reviewing this question') }}
		<x-time :time="$item->status_changed_at ?? null"/>
	</div>
@endif
