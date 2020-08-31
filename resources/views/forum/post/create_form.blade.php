@php(!empty($level) ?: $level = 0)

<div class="reply-box">
	@component('components.comment', get_defined_vars())

		@slot('avatar')
			<x-user-avatar :user="auth()->user()" width="50" height="50"/>
		@endslot

		@slot('data_attributes')
			itemscope
			itemtype="http://schema.org/Comment"
		@endslot

		<form role="form" method="post"
			  action="{{ route('posts.store', ['topic' => $topic, 'parent' => $parent ?? null]) }}">

			@csrf

			<div class="form-group">
				<label for="bb_text" class="col-form-label">
					@if (empty($parent))
						{{ __('common.your_message') }}
					@else
						{{ __('common.your_reply') }}
					@endif
				</label>
				<textarea id="bb_text" class="sceditor form-control {{ $errors->has('bb_text') ? ' is-invalid' : '' }}"
						  rows="{{ config('litlife.textarea_rows') }}" name="bb_text">{{ old('bb_text') }}</textarea>
			</div>

			<button type="submit" class="btn btn-primary">{{ __('common.create') }}</button>
		</form>

		@slot('descendants')

		@endslot

	@endcomponent
</div>