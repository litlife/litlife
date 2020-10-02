@component('components.comment', get_defined_vars())

	@slot('avatar')
		<x-user-avatar :user="$item->user" width="50" height="50"/>
	@endslot

	@slot('data_attributes')
		data-manager-id="{{ $item->id }}"
		data-manageable-id="{{ $item->manageable_id }}"
		data-manageable-type="{{ $item->manageable_type }}"
	@endslot

	<h6 class="mb-3">
		<x-user-name :user="$item->user"/>
	</h6>

	<div class=" mb-3">
		@if ($item->manageable_type == 'author')

			<span class="text-secondary">
                    {{ __('manager.character') }} "{{ __('author.manager_characters.'.$item->character) }}" {{ __('common.for') }}
                </span>

			@if (isset($item->manageable))
				<x-author-name :author="$item->manageable"/>
			@endif
		@endif
	</div>

	@if (!empty($show_comment) and !empty($item->comment))
		<div class="mb-3">
			{!! $item->comment  !!}
		</div>
	@endif

	<div class="status">
		@include('manager.alert')
	</div>

	<div class="">
		<a class="btn-start-review btn btn-outline-success" href="{{ route('managers.start_review', $item) }}"
		   @cannot ('startReview', $item) style="display:none;" @endcannot>
			{{ __('manager.start_review_request') }}
		</a>

		<a class="btn-approve btn btn-outline-success" href="{{ route('managers.approve', $item) }}"
		   @cannot ('approve', $item) style="display:none;" @endcannot>
			{{ __('common.approve') }}
		</a>

		<a class="btn-decline btn btn-outline-secondary" href="{{ route('managers.decline', $item) }}"
		   @cannot ('decline', $item) style="display:none;" @endcannot>
			{{ __('common.decline') }}
		</a>

		<a class="btn-stop-review btn btn-outline-success"
		   href="{{ route('managers.stop_review', $item) }}"
		   @cannot ('stopReview', $item) style="display:none;" @endcannot>
			{{ __('manager.stop_review_request') }}
		</a>
	</div>

	@slot('descendants')

	@endslot

@endcomponent