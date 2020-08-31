@if(count($topics) > 0)
	<div class="table-responsive">
		<table class="table table-striped table-light">
			<tr>
				<td>{{ __('topic.name') }}</td>
				<td class="text-center">{{ __('topic.post_count') }}</td>
				<td></td>
				<td>{{ __('topic.last_post') }}</td>
				<td></td>
			</tr>
			@foreach ($topics as $topic)

				@include('forum.topic.item.default', ['item' => $topic])

			@endforeach
		</table>
	</div>
	@if ($topics->hasPages())
		<div class="row">
			<div class="col-12">
				{{ $topics->appends(request()->except(['page', 'ajax']))->links() }}
			</div>
		</div>
	@endif

@else
	<div class="row">
		<div class="col-12">
			<p class="alert alert-info">{{ __('topic.nothing_found') }}</p>
		</div>
	</div>
@endif
