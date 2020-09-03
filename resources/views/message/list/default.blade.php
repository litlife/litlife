@php($level = 0)

@component('components.comment', get_defined_vars())

	@slot('anchor')
		<a id="item_{{ $item->id }}" class="anchor"></a>
	@endslot

	@slot('data_attributes')
		data-user-id="{{ $item->create_user->id }}"
	@endslot

	@slot('block_css')
		@if (!$item->isAuthUserCreator() and !$item->isViewed())
			border-info
		@endif
	@endslot

	@slot('avatar')
		<x-user-avatar :user="$item->create_user" href="0" width="50" height="50"/>
	@endslot

	<h6 class="mb-2">
		<x-user-name :user="$item->create_user"/>

		<x-time :time="$item->created_at"/>
	</h6>

	<div class="mb-2">
		<div class="html_box imgs-fluid" style="max-height: 600px; overflow-y:hidden;">
			{!! $item->text !!}
		</div>
	</div>

	<div class="">

		@if ($item->isAuthUserCreator() and !$item->isViewed())
			<button class="btn btn-light" type="button" data-toggle="tooltip" data-placement="top"
					title="{{ __('message.not_viewed') }}">
				<i class="fas fa-eye-slash"></i>
			</button>
		@endif

		@if ($item->isUpdatedByUser())
			<button class="btn btn-light" type="button" data-toggle="tooltip" data-placement="top"
					title="{{ __('message.edited_by_user',
                                    ['time' => empty($item->user_updated_at) ? '' : $item->user_updated_at->diffForHumans()]) }}">
				<i class="fas fa-user-edit"></i>
			</button>
		@endif


		<div class="d-inline-block dropdown" data-toggle="tooltip" data-placement="top"
			 title="{{ __('common.open_actions') }}">
			<button class="btn btn-light dropdown-toggle" type="button" id="dropdownMenu_{{ $item->id }}"
					data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
				<i class="fas fa-ellipsis-h"></i>
			</button>

			<button class="btn btn-light btn-compress" style="display: none;"
					data-toggle="tooltip" data-placement="top" title="{{ __('common.compress') }}">
				<i class="fas fa-compress"></i>
			</button>

			<button class="btn btn-light btn-expand" style="display: none;"
					data-toggle="tooltip" data-placement="top" title="{{ __('common.expand') }}">
				<i class="fas fa-expand"></i>
			</button>

			<div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenu_{{ $item->id }}">
				<a href="javascript:void(0)" class="delete dropdown-item text-lowercase" disabled="disabled"
				   data-loading-text="{{ __('common.deleting') }}..."
				   @cannot ('delete', $item) style="display:none;"@endcannot>
					{{ __('common.delete') }}
				</a>

				<a href="javascript:void(0)" class="restore dropdown-item text-lowercase" disabled="disabled"
				   data-loading-text="{{ __('common.restoring') }}..."
				   @cannot ('restore', $item) style="display:none;"@endcannot>
					{{ __('common.restore') }}
				</a>

				@can('update', $item)
					<a class="btn-edit text-lowercase dropdown-item" href="{{ route('messages.edit', $item) }}">
						{{ __('common.edit') }}
					</a>
				@endcan
			</div>
		</div>
	</div>


	@slot('descendants')

	@endslot

@endcomponent
