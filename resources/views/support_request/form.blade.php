<form role="form" action="{{ route('support_requests.store', ['user' => $user, 'support_request' => $supportRequest->id ?? null]) }}" method="post">

	@csrf

	<div class="form-group">
		<input id="title" name="title"
			   class="form-control {{ $errors->has('text') ? ' is-invalid' : '' }}" value="{{ old('title') }}" placeholder="{{ __('support_request.title') }}"/>
	</div>

	<div class="form-group">
		<textarea id="text" name="text"
				  class="form-control {{ $errors->has('text') ? ' is-invalid' : '' }}"
				  rows="8" placeholder="{{ __('support_request.text') }}">{{ old('text') }}</textarea>
	</div>

	<button type="submit" class="btn btn-primary">{{ __('Send') }}</button>

</form>