@if(!empty($messages) and count($messages) > 0)

	@if ($messages->hasPages())
		<div class="row mt-3">
			<div class="col-12">
				{{ $messages->appends(request()->except(['page', 'ajax']))->links() }}
			</div>
		</div>
	@endif

	@foreach ($messages as $message)

		@if (!empty($participation->new_messages_count) and ($message->id == $participation->latest_seen_message_id))

			<div class="alert alert-info mt-2 text-center" role="alert">
				<i class="fas fa-sort-up"></i> &nbsp;
				{{ __('message.new_messages') }} &nbsp;
				<i class="fas fa-sort-up"></i>
			</div>

		@endif


		@include('message.list.default', ['item' => $message])


	@endforeach

	@if ($messages->hasPages())
		<div class="row mt-3">
			<div class="col-12">
				{{ $messages->appends(request()->except(['page', 'ajax']))->links() }}
			</div>
		</div>
	@endif

@else
	<div class="row mt-3">
		<div class="col-12">
			<div class="alert alert-info">{{ __('message.nothing_found') }}</div>
		</div>
	</div>
@endif