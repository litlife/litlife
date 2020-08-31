@if (isset($folders) and $folders->count())

	<div class="list-group folders sortable mb-3">
		@foreach ($folders as $folder)

			<div class="item list-group-item list-group-item-action flex-column align-items-start @if (isset($active_folder) and $folder->id == $active_folder->id) active @endif"
				 data-id="{{ $folder->id }}">

				<div class="d-flex w-100 justify-content-between">
					<h6 class="mb-2">
						<a href="{{ route('bookmark_folders.show', ['bookmark_folder' => $folder]) }}" class="title"
						   style="color:inherit"
						   data-toggle="manual">
							{{ $folder->title }}
						</a>
					</h6>

					<span class="badge badge-pill @if (isset($active_folder) and $folder->id == $active_folder->id) badge-light @else badge-primary @endif ">{{ $folder->bookmark_count }}</span>

				</div>

				<button class="btn btn-light handle">
					<i class="fas fa-arrows-alt-v"></i>
				</button>

				<div class="btn-group" data-toggle="tooltip" data-placement="top" title="{{ __('common.open_actions') }}">
					<button class="btn btn-light dropdown-toggle" type="button" id="dropdownMenuButton"
							data-toggle="dropdown" aria-haspopup="true"
							aria-expanded="false">
						<i class="fas fa-ellipsis-h"></i>
					</button>
					<div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">

						@can ('update', $folder)

							<a class="edit dropdown-item text-lowercase"
							   href="{{ route('bookmark_folders.edit', ['bookmark_folder' => $folder]) }}">
								{{ __('common.edit') }}
							</a>

						@endcan

						<a class="delete pointer dropdown-item text-lowercase" disabled="disabled"
						   data-loading-text="{{ __('common.deleting') }}..."
						   @cannot ('delete', $folder) style="display:none;"@endcannot>
							{{ __('common.delete') }}
						</a>

						<a class="restore pointer dropdown-item text-lowercase" disabled="disabled"
						   data-loading-text="{{ __('common.restoring') }}"
						   @cannot ('restore', $folder) style="display:none;"@endcannot>
							{{ __('common.restore') }}
						</a>

					</div>
				</div>

			</div>

		@endforeach
	</div>
@else

	{{--
	<div class="alert alert-info mb-0">
		{{ __('bookmark_folder.nothing_found') }}
	</div>
 --}}
@endif