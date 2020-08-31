<div class="row mt-md-0">
	<div class="col-12">
		@if (isset($bookmarks) and $bookmarks->count())
			<div class="list-group">
				@foreach ($bookmarks as $bookmark)
					<div class="item list-group-item list-group-item-action flex-column align-items-start "
						 data-id="{{ $bookmark->id }}" data-folder_id="{{ $bookmark->folder_id }}">

						<div class="d-flex w-100 justify-content-between">
							<h6 class="mb-1">
								<a href="{{ $bookmark->url ?? $bookmark->url_old }}" data-toggle="manual" data-pk="1"
								   class="title">{{ $bookmark->title }}</a>
								<div class="model" style="height:0px; overflow:hidden"></div>
							</h6>
						</div>

						<div class="btn-group" data-toggle="tooltip" data-placement="top"
							 title="{{ __('common.open_actions') }}">
							<button class="btn btn-light dropdown-toggle" type="button" id="dropdownMenuButton"
									data-toggle="dropdown"
									aria-haspopup="true"
									aria-expanded="false">
								<i class="fas fa-ellipsis-h"></i>
							</button>
							<div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
								@can ('update', $bookmark)
									<a class="dropdown-item text-lowercase"
									   href="{{ route('bookmarks.edit', compact('bookmark')) }}" class="edit">
										{{ __('common.edit') }}
									</a>
								@endcan

								<a class="delete pointer dropdown-item text-lowercase" disabled="disabled"
								   data-loading-text="{{ __('common.deleting') }}..."
								   @cannot ('delete', $bookmark) style="display:none;"@endcannot>
									{{ __('common.delete') }}
								</a>
								<a class="restore pointer dropdown-item text-lowercase" disabled="disabled"
								   data-loading-text="{{ __('common.restoring') }}"
								   @cannot ('restore', $bookmark) style="display:none;"@endcannot>
									{{ __('common.restore') }}
								</a>

							</div>
						</div>

						<small>
							{{ __('bookmark.created_at') }}
							<x-time :time="$bookmark->created_at"/>

							@if (is_object($bookmark->folder))
								<a href="{{ route('bookmark_folders.show', $bookmark->folder) }}">"{{ $bookmark->folder->title }}
									"</a>
							@endif
						</small>

					</div>
				@endforeach
			</div>
		@else
			<p class="alert alert-info">{{ __('bookmark.nothing_found') }}</p>
		@endif
	</div>
</div>

<div class="row mt-3">
	<div class="col-12 ">
		@if (isset($bookmarks) and $bookmarks->hasPages())
			{{ $bookmarks->appends(request()->except(['page', 'ajax']))->links() }}
		@endif

	</div>
</div>