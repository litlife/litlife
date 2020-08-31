@if (empty($item->manageable) or $item->manageable->trashed())
	<div class="alert alert-info alert-rejected" role="alert">
		{{ __('manager.the_author_is_deleted') }}
	</div>
@elseif (!$item->manageable->isAccepted())
	<div class="alert alert-info alert-rejected" role="alert">
		{{ __('manager.the_author_is_not_published') }}
	</div>
@else
	@if ($item->isAccepted())
		<div class="alert alert-success alert-accepted" role="alert">
			{{ __('manager.request_approved') }}.

			@if (!empty($item->status_changed_user))
				{{ __('manager.checked_by_user') }}
				<x-user-name :user="$item->status_changed_user"/>
			@endif

			<x-time :time="$item->status_changed_at ?? null"/>
		</div>
	@endif

	@if ($item->isRejected())
		<div class="alert alert-info alert-rejected" role="alert">
			{{ __('manager.declined') }}.

			@if (!empty($item->status_changed_user))
				{{ __('manager.checked_by_user') }}
				<x-user-name :user="$item->status_changed_user"/>
			@endif

			<x-time :time="$item->status_changed_at ?? null"/>
		</div>
	@endif

	@if ($item->isReviewStarts())
		<div class="alert alert-info alert-rejected" role="alert">
			{{ __('manager.request_review_by_user') }}

			@if (!empty($item->status_changed_user))
				<x-user-name :user="$item->status_changed_user"/>
			@endif

			<x-time :time="$item->status_changed_at ?? null"/>
		</div>
	@endif
@endif