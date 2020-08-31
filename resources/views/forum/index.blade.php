@extends('layouts.app')

@push('scripts')

	<script src="{{ mix('js/forums.js', config('litlife.assets_path')) }}"></script>

@endpush

@section('content')

	@include('scripts.jquery-sortable')

	<div class="forum_groups">

		@foreach ($forumGroups as $forumGroup)

			<div class="card forum_group  mb-3" data-id="{{ $forumGroup->id }}">
				<div class="card-header">
					<a id="group_{{ $forumGroup->id }}" class="anchor"></a>

					<div class="d-flex ">
						<div class="d-flex flex-row ml-2 mr-auto align-items-center">
							@include('forum.group.icon')

							<h3 class="h5 mb-0">
								{{ $forumGroup->name }}
							</h3>
						</div>

						@can ('change_order', $forumGroup)
							<button class="move_group pull-right btn btn-light mr-1">
								<i class="fas fa-arrows-alt-v"></i>
							</button>
						@endcan

						<div class="d-inline">
							<button class="btn btn-light dropdown-toggle mr-1" type="button" id="dropdownMenuButton"
									data-toggle="dropdown"
									aria-haspopup="true"
									aria-expanded="false">
								<i class="fas fa-ellipsis-h"></i>
							</button>
							<div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
								@can ('create', App\Forum::class)
									<a class="dropdown-item"
									   href="{{ route('forums.create', ['forum_group_id' => $forumGroup->id]) }}">
										{{ __('forum.çreate') }}
									</a>
								@endcan
								@can ('update', $forumGroup)
									<a class="dropdown-item"
									   href="{{ route('forum_groups.edit', ['forum_group' => $forumGroup]) }}">
										{{ __('common.edit') }}
									</a>
								@endcan
								@can ('delete', $forumGroup)
									<a class="dropdown-item"
									   href="{{ route('forum_groups.destroy', ['forum_group' => $forumGroup]) }}">
										{{ __('common.delete') }}
									</a>
								@endcan
							</div>
						</div>

					</div>
				</div>
				<div class="table-responsive">
					<table class="forums table table-light">
						<thead class="thead-light">
						<tr>
							<th>{{ __('forum.name') }}</th>
							<th class="text-center">{{ __('forum.topic_count') }}</th>
							<th class="text-center">{{ __('forum.post_count') }}</th>
							<th class="text-center"></th>
							<th>{{ __('forum.last_post') }}</th>
							<th></th>
						</tr>
						</thead>
						<tbody>
						@foreach ($forumGroup->forums as $forum)
							@can('view', $forum)
								@include('forum.forum.item.default', ['forum' => $forum])
							@endcan
						@endforeach
						</tbody>
					</table>
				</div>
			</div>

		@endforeach

	</div>

@endsection
