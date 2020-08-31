<select class="read-status inline custom-select" style="width:200px;">
	<option value="null"> -</option>
	@foreach (\App\Enums\ReadStatus::getKeys() as $status)
		<option value="{{ $status }}"
				@if ((isset($user_read_status->status)) && ($user_read_status->status == $status)) selected @endif>
			{{ __('book.status.'.$status) }}
		</option>
	@endforeach
</select>