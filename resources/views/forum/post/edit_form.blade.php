<form role="form" method="POST"
	  action="{{ route('posts.update', compact('post')) }}">

	@csrf
	@method('patch')

	<div class="form-group">

		<label for="bb_text" class="col-form-label">
			{{ __('post.bb_text') }}
		</label>

		<textarea id="bb_text" name="bb_text"
				  class="sceditor form-control"
				  rows="{{ config('litlife.textarea_rows') }}">{{ old('bb_text') ?? $post->bb_text  }}</textarea>

	</div>

	<button type="submit" class="btn btn-primary">{{ __('common.save') }}</button>

</form>