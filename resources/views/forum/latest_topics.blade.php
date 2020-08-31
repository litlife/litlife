@if (!empty($topics) and $topics->count() > 0)

	<div class="mb-2 px-3">
		<a class="text-decoration-none" href="{{ route('topics.index', ['order' => 'last_post_created_at_desc']) }}">
			<i class="fas fa-fire" style="color:red"></i> {{ __('topic.recent_topics_discussed') }}
		</a>
	</div>

	<div class="list-group">
		@foreach ($topics as $topic)
			@include('forum.topic.item.short', [
			'item' => $topic,
			'main_priority_show' => true
			])
		@endforeach
	</div>
@endif
