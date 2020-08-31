@extends('layouts.app')

@push('scripts')

	<script src="{{ mix('js/latest-posts.js', config('litlife.assets_path')) }}"></script>

@endpush

@section('content')

	@include('home.navbar')

	@if ($posts->currentPage() < 2)

		<div class="table-responsive">
			<table class="topics table table-light table-striped">

				<thead>
				<tr>
					<td>{{ __('topic.name') }}</td>
					<td class="text-center ">{{ __('topic.post_count') }}</td>
					<td></td>
					<td>{{ __('topic.last_post') }}</td>
					<td></td>
				</tr>
				</thead>

				<tbody>

				@if (!empty($topics))
					@foreach ($topics as $topic)
						@include('forum.topic.item.default', [
						'item' => $topic,
						'main_priority_show' => true
						])
					@endforeach
				@else
					<tr>
						<td style="width:100%">
							{{ __('topic.nothing_found') }}
						</td>
					</tr>
				@endif

				</tbody>
			</table>
		</div>

		<div class="col-12 mb-3 text-truncate text-center">
			<a class="btn btn-light" href="{{ route('topics.index', ['order' => 'last_post_created_at_desc']) }}">
				{{ __('topic.next_popular_topics') }}
			</a>
		</div>

	@endif

	@if ($posts->hasPages())
		{{ $posts->appends(request()->except(['page', 'ajax']))->links() }}
	@endif

	<div class="posts">
		@foreach ($posts as $post)
			@include('forum.post.item.default', [
			'item' => $post,
			'no_limit' => true,
			'no_select' => true,
			'achievements' => true
			])
		@endforeach
	</div>

	@if ($posts->hasPages())
		{{ $posts->appends(request()->except(['page', 'ajax']))->links() }}
	@endif

@endsection