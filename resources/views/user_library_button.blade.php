@php($pressed = (isset($user_library) and Auth::check()))

<button class="user_library btn btn-outline-secondary"
		type="button"
		@if ($pressed)
		aria-pressed="true"
		@else
		aria-pressed="false"
		@endif
		autocomplete="off"
		@if (!empty($tooltip_pressed) and !$pressed)
		data-title="{{ $tooltip_pressed }}"
		data-toggle="tooltip"
		@endif
		data-type="{{ $type }}"
		data-id="{{ $item->id }}">

    <span data-status="exists" @if (!$pressed) style="display:none" @endif>
        <i class="fas fa-star"></i>
        <span class="text">{{ __('common.in_favorites') }}</span>
    </span>
	<span data-status="not_exists" @if ($pressed) style="display:none" @endif>
        <i class="far fa-star"></i>&nbsp;
        <span class="text">{{ __('common.add_to_favorites') }}</span>
    </span>
	<span data-status="loading" style="display:none">&nbsp;
        <i class="fas fa-spinner fa-spin"></i>
        <span class="text">{{ __('common.processing') }}...</span>
    </span>

	<span class="ml-2 count badge badge-light" style="display: none;">
		{{ $count }}
	</span>

</button>