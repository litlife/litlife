<button class="btn-favorite btn btn-light"
		type="button"
		data-toggle="button"
		aria-pressed="true" autocomplete="off"
		data-type="{{ $type }}"
		data-url="{{ route('collections.favorite.toggle', ['collection' => $item]) }}"
		data-id="{{ $item->id }}">

    <span data-status="filled" @if (!isset($favorite) or !Auth::check()) style="display:none" @endif>
        <i class="fas fa-star"></i>
        <span class="count ml-1">{{ $item->added_to_favorites_users_count }}</span>
    </span>
	<span data-status="empty" @if (isset($favorite) and Auth::check()) style="display:none" @endif>
        <i class="far fa-star"></i>
        <span class="count ml-1">{{ $item->added_to_favorites_users_count }}</span>
    </span>
	<span data-status="wait" style="display:none">&nbsp;
        <i class="fas fa-spinner fa-spin"></i>
        <span class="count ml-1">{{ $item->added_to_favorites_users_count }}</span>
    </span>

</button>