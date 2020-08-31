@component('components.comment', get_defined_vars())

	@slot('avatar')
		<x-user-avatar :user="$item->create_user" width="50" height="50"/>
	@endslot

	@slot('data_attributes')
		data-request-id="{{ $item->id }}"
		data-author-id="{{ $item->author_id }}"
		data-manager-id="{{ $item->manager_id }}"
	@endslot

	<h6 class="mb-3">
		<x-user-name :user="$item->create_user"/>
	</h6>

	<div class="row mb-3">
		<div class="col-12">
			{{ trans_choice('author.authors', 1) }}:
			<x-author-name :author="optional($item->manager)->manageable"/>
		</div>
	</div>

	@if ($item->isReviewStarts())
		<div class="row mb-3">
			<div class="col-12">
				{{ __('author_sale_request.start_review_user') }}
				<x-user-name :user="$item->status_changed_user"/>
				<x-time :time="$item->status_changed_at"/>
			</div>
		</div>
	@elseif ($item->isAccepted())
		<div class="row mb-3">
			<div class="col-12">
				{{ __('author_sale_request.accept_user') }}
				<x-user-name :user="$item->status_changed_user"/>
				<x-time :time="$item->status_changed_at"/>
			</div>
		</div>
	@elseif ($item->isRejected())
		<div class="row mb-3">
			<div class="col-12">
				{{ __('author_sale_request.reject_user') }}
				<x-user-name :user="$item->status_changed_user"/>
				<x-time :time="$item->status_changed_at"/>
			</div>
		</div>
	@endif

	<div class="row">
		<div class="col-12">
			<div class="alert alert-success alert-accepted" role="alert"
				 style="@if (!$item->isAccepted()) display: none @endif">
				{{ __('author_sale_request.request_accepted') }}
			</div>

			<div class="alert alert-info alert-rejected" role="alert"
				 style="@if (!$item->isRejected()) display: none @endif">
				{{ __('author_sale_request.request_rejected') }}
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-12">

			@can ('start_review', $item)
				<a href="{{ route('authors.sales_requests.start_review', ['request' => $item->id]) }}"
				   class="starts_review btn btn btn-outline-success">
					{{ __('common.start_review') }}
				</a>
			@endcan

			@can ('continue_review', $item)
				<a href="{{ route('authors.sales_requests.show', ['request' => $item->id]) }}"
				   class="starts_review btn btn btn-outline-secondary">
					{{ __('common.continue_review') }}
				</a>
			@endcan

			@if ($item->isAccepted() or $item->isRejected())
				<a href="{{ route('authors.sales_requests.show', ['request' => $item->id]) }}"
				   class="starts_review btn btn btn-outline-secondary">
					{{ __('author_sale_request.view_request') }}
				</a>
			@endif
		</div>
	</div>

	@slot('descendants')

	@endslot

@endcomponent