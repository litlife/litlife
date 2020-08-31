@extends('layouts.app')

@push('scripts')
	<script src="{{ mix('js/collections.comments.js', config('litlife.assets_path')) }}"></script>
@endpush

@section ('content')

	@include('collection.show_navbar')

	@if (session('success'))
		<div class="alert alert-success alert-dismissable">
			{{ session('success') }}
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		</div>
	@endif

	<div class="mb-2">
		@component('components.bell_toggle_button', ['type' => 'collection',
		'id' => $collection->id,
		'item' => $collection,
		'subscription' => $subscription ?? null
		])
			@slot('url')
				{{ route('collections.event_notification_subcriptions.toggle', $collection) }}
			@endslot
			@slot('filled_button_content')
				<i class="far fa-bell-slash"></i> {{ __('collection.disable_notify_on_new_comments') }}
			@endslot
			@slot('empty_button_content')
				<i class="far fa-bell"></i> {{ __('collection.notify_on_new_comments') }}
			@endslot
		@endcomponent
	</div>

	@can('commentOn', $collection)
		<div class="form_create_comment mb-3">

			@include('comment.create_form', ['commentable_type' => 18, 'commentable_id' => $collection->id])

		</div>
	@endcan

	@if (isset($comments) and count($comments) > 0)

		@if ($comments->hasPages())
			<div class="row mt-3">
				<div class="col-12">
					{{ $comments->appends(request()->except(['page', 'ajax']))->links() }}
				</div>
			</div>
		@endif

		<div class="comments">
			@foreach ($comments as $comment)
				@include("comment.list.default", ['item' => $comment, 'no_book_link' => true])
			@endforeach
		</div>

		@if ($comments->hasPages())
			<div class="row">
				<div class="col-12">
					{{ $comments->appends(request()->except(['page', 'ajax']))->links() }}
				</div>
			</div>
		@endif

	@else
		@if (empty($top_comments) or count($top_comments) < 1)
			<div class="row">
				<div class="col-12">
					<div class="alert alert-info">
						{{ __('comment.nothing_found') }}
					</div>
				</div>
			</div>
		@endif
	@endif

@endsection

