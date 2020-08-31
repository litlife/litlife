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
			  action="{{ route('comments.store', ['commentable_type' => $commentable_type, 'commentable_id' => $commentable_id, 'parent' => $parent ?? null]) }}">

			@csrf

			<div class="form-group{{ $errors->has('bb_text') ? ' has-error' : '' }}">
            <textarea id="bb_text" class="sceditor form-control" rows="{{ config('litlife.textarea_rows') }}"
					  name="bb_text"></textarea>
			</div>

			@if (!empty($canLeaveCommentInPersonalAccess))
				<div class="form-group form-check">
					<input name="leave_for_personal_access" type="checkbox"
						   class="form-check-input" id="leave_for_personal_access" value="1">
					<label class="form-check-label" for="leave_for_personal_access">{{ __('comment.leave_for_personal_access') }}</label>
					<a href="javascript:void(0)"
					   data-container="body" data-toggle="popover" data-placement="top" data-content="{{ __('comment.leave_for_personal_access_tooltip') }}"
					   class="btn btn-sm btn-light">
						<i class="fas fa-question"></i>
					</a>
				</div>
			@endif

			<button type="submit" class="btn btn-primary">{{ __('common.send') }}</button>
		</form>

		@slot('descendants')

		@endslot

	@endcomponent
</div>