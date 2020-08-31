<button class="btn-bell-toggle btn btn-light"
		type="button"
		data-toggle="button"
		aria-pressed="true"
		data-type="{{ $type }}"
		data-url="{{ $url }}"
		data-id="{{ $item->id }}">
    <span data-status="filled" @if (!isset($subscription) or !Auth::check()) style="display:none" @endif>
         {{ $filled_button_content ?? '' }}
    </span>
	<span data-status="empty" @if (isset($subscription) and Auth::check()) style="display:none" @endif>
        {{ $empty_button_content ?? '' }}
    </span>
	<span data-status="wait" style="display:none">
        <i class="fas fa-spinner fa-spin"></i>
    </span>
</button>