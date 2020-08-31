@php(!empty($level) ?: $level = 0)

<div class="reply-box">
	@component('components.comment', get_defined_vars())

		@slot('anchor')
			@isset($item)
				<span id="blog_{{ $item->id }}" class="anchor"></span>
			@endisset
		@endslot

		@slot('data_attributes')
			@isset($item)
				@if (isset($parent))
					data-parent-id="{{ $parent->id }}"
				@endif

				@if (!empty($item->owner))
					data-user-id="{{ $item->owner->id }}"
				@endif
				data-level="{{ $level }}"
			@endisset
			itemscope
			itemtype="http://schema.org/Comment"
		@endslot

		@slot('avatar')
			<x-user-avatar :user="auth()->user()" width="50" height="50"/>
		@endslot

		<form role="form" method="POST"
			  action="{{ route('users.blogs.store', ['user' => $user, 'parent' => $parent ?? null]) }}">

			@csrf

			<div class="form-group{{ $errors->has('bb_text') ? ' has-error' : '' }}">
            <textarea id="bb_text" class="sceditor form-control" rows="{{ config('litlife.textarea_rows') }}"
					  name="bb_text"></textarea>
			</div>

			@if (empty($parent))
				<div class="form-group form-check">
					<input name="display_on_home_page" type="hidden" value="0"/>
					<input name="display_on_home_page" type="checkbox" class="form-check-input" value="1">
					<label class="form-check-label">{{ __('blog.display_on_home_page') }}</label>
				</div>
			@endif

			<button type="submit" class="btn btn-primary">{{ __('common.create') }}</button>
		</form>

		@slot('descendants')

		@endslot

	@endcomponent
</div>