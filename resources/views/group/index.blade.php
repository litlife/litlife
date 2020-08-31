@extends('layouts.app')

@push('scripts')

	<script src="{{ mix('js/group.index.js', config('litlife.assets_path')) }}"></script>

@endpush

@section('content')

	<div class="card">
		<div class="card-body">

			<div class="table-responsive">
				<table class="groups table table-striped">
					@foreach ($groups as $group)

						<tr class="item" data-id="{{ $group->id }}">
							<td>
								{{ $group->id }}
							</td>
							<td>
								<a href="{{ route('users', ['group' => $group->id]) }}">{{ $group->name }}</a>
							</td>

							<td>

								<div class="btn-group">
									<button class="btn btn-light dropdown-toggle" type="button" id="dropdownMenuButton"
											data-toggle="dropdown"
											aria-haspopup="true"
											aria-expanded="false">
										<i class="fas fa-ellipsis-h"></i>
									</button>
									<div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">

										@can ('update', $group)
											<a class="dropdown-item" href="{{ route('groups.edit', compact('group')) }}">
												{{ __('common.edit') }}
											</a>
										@endcan

										<a class="delete pointer dropdown-item" disabled="disabled"
										   data-loading-text="{{ __('common.deleting') }}..."
										   @cannot ('delete', $group) style="display:none;"@endcannot>
											{{ __('common.delete') }}
										</a>

										<a class="restore pointer dropdown-item" disabled="disabled"
										   data-loading-text="{{ __('common.restoring') }}"
										   @cannot ('restore', $group) style="display:none;"@endcannot>
											{{ __('common.restore') }}
										</a>

									</div>
								</div>
							</td>
						</tr>

					@endforeach

				</table>
			</div>
			@can('create', App\UserGroup::class)

				<a class="btn btn-primary" href="{{ route('groups.create') }}">
					{{ __('common.create') }}
				</a>
			@endcan
		</div>
	</div>

	@if ($groups->hasPages())
		<div class="row mt-3">
			<div class="col-12">
				{{ $groups->appends(request()->except(['page', 'ajax']))->links() }}
			</div>
		</div>
	@endif

@endsection