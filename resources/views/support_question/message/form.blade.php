<form role="form" action="{{ route('support_question_messages.store', ['support_question' => $supportQuestion->id]) }}" method="post">

	@csrf

	<div class="form-group">
		<textarea id="bb_text" name="bb_text"
				  class="sceditor form-control {{ $errors->has('bb_text') ? ' is-invalid' : '' }}"
				  rows="{{ config('litlife.textarea_rows') }}" placeholder="{{ __('support_question.bb_text') }}">{{ old('bb_text') }}</textarea>
	</div>

	<button type="submit" class="btn btn-primary">{{ __('Reply') }}</button>

</form>