<div class="like d-inline-block" data-likeable-type="{{ $likeable_type }}"
	 @if (!empty($likeable_type->create_user_id)) data-likeable-create-user-id="{{ $likeable_type->create_user_id }}"
	 @endif
	 data-likeable-id="{{ $item->id }}" data-liked="true" data-toggle="popover" data-html="true"
	 title='<a target="_blank" href="{{ route('likes.users', ['type' => $likeable_type, 'id' => $item->id]) }}">{{ __('like.users') }}</a>'>

	<button class="liked btn btn-light" type="button"
			@if (!$like) style="display: none;" @endif>
		<i class="fas fa-heart" style="color:red"></i>
		<span class="counter"
			  @if (empty($item->like_count)) style="display:none;" @endif>{{ intval($item->like_count) }}</span>
	</button>

	<button class="empty btn btn-light" type="button"
			@if ($like) style="display: none;" @endif>
		<i class="far fa-heart"></i>
		<span class="counter"
			  @if (empty($item->like_count)) style="display:none;" @endif>{{ intval($item->like_count) }}</span>
	</button>

</div>
