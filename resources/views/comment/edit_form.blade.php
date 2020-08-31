<form role="form" method="POST"
	  action="{{ route('comments.update', compact('comment')) }}">

	@csrf
	@method('patch')

	<div class="form-group">
		<label for="bb_text" class="col-form-label">{{ __('comment.bb_text') }}</label>
		<textarea id="bb_text" class="sceditor form-control" rows="{{ config('litlife.textarea_rows') }}"
				  name="bb_text">{{ old('bb_text') ?? $comment->bb_text  }}</textarea>
	</div>

	<button type="submit" class="btn btn-primary">
		{{ __('common.save') }}
	</button>

</form>
