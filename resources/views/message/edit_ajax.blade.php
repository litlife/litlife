<form role="form" method="post"
	  action="{{ route('messages.update', ['user' => $message->recepient_id, 'message' => $message]) }}">

	@csrf
	@method('patch')

	<div class="row form-group">
		<label for="bb_text"></label>
		<div class="col-12">
            <textarea id="bb_text" class="sceditor form-control" rows="{{ config('litlife.textarea_rows') }}"
					  name="bb_text">{{ old('bb_text') ?? $message->bb_text  }}</textarea>
			@if ($errors->has('bb_text')) <p class="help-block">{{ $errors->first('bb_text') }}</p> @endif
		</div>
	</div>

	<div class="row form-group">
		<div class="col-12">
			<button type="submit" class="btn btn-primary">{{ __('common.save') }}</button>
		</div>
	</div>

	<div class="row form-group">
		<div class="col-12">
			{{ __('message.can_edited_warning', ['time' => $message->created_at->addMinutes(config('litlife.time_that_can_edit_message'))->diffForHumans(null, true)]) }}
		</div>
	</div>

</form>