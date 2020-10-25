<form role="form" action="{{ route('support_request_messages.store', ['support_request' => $supportRequest->id ?? null]) }}" method="post">

	@csrf

	<div class="form-group">
		<label for="text" class="col-form-label">{{ __('support_request_message.text') }}</label>
		<textarea id="text" name="text"
				  class="form-control {{ $errors->has('text') ? ' is-invalid' : '' }}"
				  rows="8">{{ old('text') }}</textarea>
	</div>

	<button type="submit" class="btn btn-primary">{{ __('Send') }}</button>

</form>