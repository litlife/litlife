@php(!empty($level) ?: $level = 0)

<div class="reply-box">
	@component('components.comment', get_defined_vars())

		@slot('avatar')
			<x-user-avatar :user="auth()->user()" width="50" height="50"/>
		@endslot

		<form role="form" method="post"
			  action="{{ route('users.messages.store', ['user' => $user]) }}">

			@csrf

			<div class="form-group">
				<label for="bb_text">{{ __('common.your_message') }}</label>
				<textarea id="bb_text" name="bb_text"
						  class="sceditor form-control{{ $errors->has('bb_text') ? ' is-invalid' : '' }}"
						  rows="{{ config('litlife.textarea_rows') }}">{{ old('bb_text') ?? ''  }}</textarea>
			</div>

			@if ($errors->any())
				<div class="alert alert-danger">
					<ul>
						@foreach ($errors->all() as $error)
							<li>{{ $error }}</li>
						@endforeach
					</ul>
				</div>
			@endif

			<button type="submit" class="btn btn-primary">{{ __('common.send') }}</button>
		</form>

		@slot('descendants')

		@endslot

	@endcomponent
</div>