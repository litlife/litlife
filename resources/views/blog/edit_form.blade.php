<form role="form" method="POST" enctype="multipart/form-data"
	  action="{{ route('users.blogs.update', compact('user', 'blog')) }}">

	@csrf
	@method('patch')

	@if ($errors->any())
		<div class="alert alert-danger">
			<ul>
				@foreach ($errors->all() as $error)
					<li>{{ $error }}</li>
				@endforeach
			</ul>
		</div>
	@endif

	<div class="form-group{{ $errors->has('bb_text') ? ' has-error' : '' }}">
		<label for="bb_text" class="col-form-label">{{ __('blog.bb_text') }}</label>

		<textarea id="bb_text" class="sceditor form-control" rows="{{ config('litlife.textarea_rows') }}"
				  name="bb_text">{!! $blog->bb_text !!}</textarea>

	</div>

	@if ($blog->isRoot())

		<div class="form-group form-check">
			<input name="display_on_home_page" type="hidden" value="0"/>
			<input name="display_on_home_page" type="checkbox" class="form-check-input" value="1">
			<label class="form-check-label">{{ __('blog.display_on_home_page') }}</label>
		</div>

	@endif

	<button type="submit" class="btn btn-primary">{{ __('common.save') }}</button>

</form>