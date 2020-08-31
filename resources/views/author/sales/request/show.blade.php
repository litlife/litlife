@extends('layouts.app')

@section('content')

	@isset($item)
		@if ($item->isRejected())
			<div class="alert alert-info">
				{{ __('author_sale_request.request_rejected') }}.
				{{ __('author_sale_request.you_can_submit_a_new_application_in_days', ['days' => config('litlife.minimum_days_to_submit_a_new_request_for_author_sale')]) }}
			</div>
		@elseif ($item->isAccepted())
			<div class="alert alert-success">
				{{ __('author_sale_request.request_accepted') }}
			</div>
		@elseif ($item->isSentForReview())
			<div class="alert alert-info">
				{{ __('author_sale_request.wait_for_review') }}
			</div>
		@endif
	@endif

	<div class="card">
		<div class="card-body">
			<div>
				{{ __('author_sale_request.from_user') }}:
				<x-user-name :user="$item->create_user"/>
				<x-time :time="$item->created_at"/>
			</div>
			<div>
				{{ __('author_sale_request.for_author') }}:
				<x-author-name :author="$item->author"/>
			</div>

			<div class="">
				@if ($item->isReviewStarts())
					<div class="row ">
						<div class="col-12">
							{{ __('author_sale_request.start_review_user') }}:
							<x-user-name :user="$item->status_changed_user"/>
							<x-time :time="$item->status_changed_at"/>
						</div>
					</div>
				@elseif ($item->isAccepted())
					<div class="row ">
						<div class="col-12">
							{{ __('author_sale_request.accept_user') }}:
							<x-user-name :user="$item->status_changed_user"/>
							<x-time :time="$item->status_changed_at"/>
						</div>
					</div>
				@elseif ($item->isRejected())
					<div class="row ">
						<div class="col-12">
							{{ __('author_sale_request.reject_user') }}:
							<x-user-name :user="$item->status_changed_user"/>
							<x-time :time="$item->status_changed_at"/>
						</div>
						<div class="col-12">
							{{ __('author_sale_request.review_comment') }}:
							{{ $item->review_comment }}
						</div>
					</div>
				@endif
			</div>

			<div class="row">
				<div class="col-12">
					{{ __('author_sale_request.text') }}: {{ $item->text }}
				</div>
			</div>

			<div class="">
				@can ('accept', $item)
					<a href="{{ route('authors.sales_requests.accept', ['request' => $item]) }}"
					   class="approve btn btn btn-outline-success">
						{{ __('common.approve') }}
					</a>
				@endcan

				@can ('stop_review', $item)
					<a href="{{ route('authors.sales_requests.stop_review', ['request' => $item]) }}"
					   class="decline btn btn btn-outline-secondary">
						{{ __('common.stop_review') }}
					</a>
				@endcan
			</div>

			@can ('reject', $item)
				<form class="mt-3" role="form" method="POST"
					  action="{{ route('authors.sales_requests.reject', ['request' => $item]) }}"
					  enctype="multipart/form-data">
					@csrf

					<div class="form-group{{ $errors->has('review_comment') ? ' has-error' : '' }}">
						<label class="col-form-label"
							   for="review_comment">{{ __('author_sale_request.review_comment') }}</label>
						<div class="">
                        <textarea id="review_comment"
								  class="form-control{{ $errors->has('review_comment') ? ' is-invalid' : '' }}" rows="5"
								  name="review_comment">{{ old('review_comment') }}</textarea>
						</div>
					</div>

					<button type="submit" class="btn btn-outline-danger">{{ __('common.decline') }}</button>
				</form>
			@endcan

		</div>
	</div>

@endsection