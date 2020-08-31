@extends('layouts.app')

@push('scripts')

	<script src="{{ mix('js/topic.index.js', config('litlife.assets_path')) }}"></script>

@endpush

@section('content')

	<div class="forum-container">

		@if (!empty($forum->description))

			<div class="card  mb-3">
				<div class="card-body">
					{{ $forum->description }}
				</div>
			</div>

		@endif

		<div class="row">
			<div class="col-12 d-flex">

				@can ('create_topic', $forum)
					<a class="btn btn-primary mb-3 mr-3" href="{{ route('topics.create', compact('forum')) }}">
						{{ __('topic.create') }}
					</a>
				@endcan

				@if ($forum->min_message_count > 0)
				<!--noindex-->
					<small class="text-muted  mb-3">
						{{ __('forum.topic_create_limitation_warning', ['min_message_count' => $forum->min_message_count]) }}
					</small>
					<!--/noindex-->
				@endif

			</div>
		</div>

		@if (!empty($topics) and ($topics->count() > 0))
			<div class="row">
				<div class="col-12">
					<div class="table-responsive">
						<table class="table table-light table-striped">
							<thead class="thead-light">
							<tr>
								<th>{{ __('topic.name') }}</th>
								<th class="text-center">{{ __('topic.post_count') }}</th>
								<th></th>
								<th>{{ __('forum.last_post') }}</th>
								<th></th>
							</tr>
							</thead>
							<tbody>
							@foreach ($topics as $topic)
								@include('forum.topic.item.default', [
								'item' => $topic,
								'forum_priority_show' => true
								])
							@endforeach
							</tbody>
						</table>
					</div>
				</div>
			</div>

			<div class="row">
				<div class="col-12">
					{{ $topics->appends(request()->except(['page', 'ajax']))->links() }}
				</div>
			</div>
		@else

			<div class="row">
				<div class="col-12">
					<div class="alert alert-info">{{ __('topic.nothing_found') }}</div>
				</div>
			</div>

		@endif


	</div>

@endsection
