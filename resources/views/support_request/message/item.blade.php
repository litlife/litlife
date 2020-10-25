@php($level = 0)

@component('components.comment', get_defined_vars())

	@slot('anchor')
		<a id="{{ $item->getAnchorId() }}" class="anchor"></a>
	@endslot

	@slot('data_attributes')
		data-user-id="{{ $item->create_user->id }}"
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
		</div>
	</div>

@endcomponent
