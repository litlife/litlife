@extends('layouts.app')

@push('scripts')

	<script src="{{ mix('js/topics.show.js', config('litlife.assets_path')) }}"></script>

@endpush

@section('content')

	@if ($topic->isArchived())
		<div class="alert alert-warning" role="alert">
			{{ __('topic.archived') }}
			@can ('unarchive', $topic)
				<a href="{{ route('topics.unarchive', compact('topic')) }}"
				   class="alert-link">{{ __('common.unarchive') }}</a>
			@endcan
		</div>
	@endif

	@if ($topic->top_post)

		<div class="fixed_post">
			@include ('forum.post.item.default', [
			'item' => $topic->top_post,
			'no_topic_forum_links' => true,
			'no_go_to_forum_button' => true,
			'achievements' => true
			])
		</div>

	@endif

	@if ($topic->post_desc)
		@can ('create_post', $topic)
			@include ('forum.post.create_form')
		@endcan
	@endif

	<div class="list">
		@include('forum.topic.show_ajax')
	</div>

	@if (!$topic->post_desc)
		@can ('create_post', $topic)
			@include ('forum.post.create_form')
		@endcan
	@endif

	<div>

		@component('components.bell_toggle_button', ['type' => 'topic',
  'id' => $topic->id,
  'item' => $topic,
  'subscription' => $topic->auth_user_subscription ?? null
  ])
			@slot('url')
				{{ route('topics.subscribe', $topic) }}
			@endslot
			@slot('filled_button_content')
				<i class="far fa-bell-slash"></i> {{ __('topic.disable_notify_on_new_posts') }}
			@endslot
			@slot('empty_button_content')
				<i class="far fa-bell"></i> {{ __('topic.notify_on_new_posts') }}
			@endslot
		@endcomponent

		<a class="btn btn-light" href="{{ route('topics.posts.index', $topic) }}">
			{{ __('common.seek') }}
		</a>

		@can ('open', $topic)
			<a class="btn btn-light" href="{{ route('topics.open', $topic) }}">
				{{ __('common.open') }}
			</a>
		@elsecan ('close', $topic)
			<a class="btn btn-light" href="{{ route('topics.close', $topic) }}">
				{{ __('common.close') }}
			</a>
		@endcan

		@can ('move', App\Post::class)
			<button class="move btn btn-light" title="{{ __('topic.move_tooltip') }}" data-toggle="tooltip">
				{{ __('common.move') }}
			</button>
		@endcan

		@can ('merge', $topic)
			<a href="{{ route('topics.merge_form', compact('topic')) }}" class="btn btn-light"
			   title="{{ __('topic.merge_tooltip') }}"
			   data-toggle="tooltip">
				{{ __('common.merge') }}
			</a>
		@endcan

	</div>


@endsection
