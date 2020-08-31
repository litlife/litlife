@extends('layouts.app')

@section('content')

	<div class="forum-container">

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