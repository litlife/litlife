<a href="{{ route('support_requests.show', ['support_request' => $item->id]) }}" class="list-group-item list-group-item-action">
	<div class="d-flex w-100 justify-content-between">
		<h5 class="mb-1">#{{ $item->id }} {{ $item->title }}</h5>
		<small>

			<x-time :time="$item->latest_message->created_at"/>
		</small>
	</div>
	<p class="mb-1">
		@if ($item->latest_message->create_user_id == auth()->id())
			{{ __('You') }}:
		@else
			<x-user-name :user="$item->latest_message->create_user" href="0"/>:
		@endif

		{{ \Illuminate\Support\Str::limit($item->latest_message->text, 200) }}
	</p>
	<div>
		@if ($item->isAccepted())
			<span class="badge badge-success">{{ __('Request resolved') }}</span>
		@else
			@if ($item->isLatestMessageByCreatedUser())
				<span class="badge badge-info">{{ __('Expect an answer') }}</span>
			@else
				<span class="badge badge-info">{{ __('There is an answer') }}</span>
			@endif
		@endif
	</div>
</a>