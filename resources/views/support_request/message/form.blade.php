<form role="form" action="{{ route('support_request_messages.store', ['support_request' => $supportRequest->id]) }}" method="post">

	@csrf

	<div class="form-group">
		<textarea id="text" name="text"
				  class="form-control {{ $errors->has('text') ? ' is-invalid' : '' }}"
				  rows="8" placeholder="{{ __('support_request.text') }}">{{ old('text') }}</textarea>
	</div>

	<button type="submit" class="btn btn-primary">{{ __('Reply') }}</button>

</form>