@extends('layouts.app')

@section('content')
	@if ($errors->any())
		<div class="alert alert-danger">
			<ul class="mb-0">
				@foreach ($errors->all() as $error)
					<li>{{ $error }}</li>
				@endforeach
			</ul>
		</div>
	@endif
	<div class="card">
		<div class="card-body">

			<form role="form" action="{{ route('topics.store', compact('forum')) }}" method="post">

				@csrf


				<div class="form-group">
					<label for="name" class="col-form-label">{{ __('topic.name') }}</label>
					<input id="name" name="name"
						   class="form-control {{ $errors->has('name') ? ' is-invalid' : '' }}"
						   type="text" value="{{ old('name') }}"/>
				</div>

				<div class="form-group">
					<label for="description" class="col-form-label">{{ __('topic.description') }}</label>
					<textarea id="description" name="description"
							  class="form-control {{ $errors->has('description') ? ' is-invalid' : '' }}"
							  rows="3">{{ old('description') }}</textarea>
				</div>

				@can ('edit_spectial_settings', \App\Topic::class)

					<div class="form-group">
						<label for="forum_priority" class="col-form-label">{{ __('topic.forum_priority') }}</label>
						<input id="forum_priority" name="forum_priority"
							   class="form-control {{ $errors->has('forum_priority') ? ' is-invalid' : '' }}"
							   type="text" value="{{ old('forum_priority') ?: '0' }}"/>
					</div>

					<div class="form-group">
						<label for="main_priority" class="col-form-label">{{ __('topic.main_priority') }}</label>
						<input id="main_priority" name="main_priority"
							   class="form-control {{ $errors->has('main_priority') ? ' is-invalid' : '' }}"
							   type="text" value="{{ old('main_priority') ?: '0' }}"/>
					</div>

					<div class="form-group">
						<div class="form-check">
							<input name="post_desc" type="hidden" value="0"/>
							<input id="post_desc_check" name="post_desc" type="checkbox"
								   class="form-check-input {{ $errors->has('post_desc') ? ' is-invalid' : '' }}"
								   @if (old('post_desc')) checked="checked" @endif
								   value="1"/>
							<label class="form-check-label" for="post_desc_check">
								{{ __('topic.post_desc') }}
							</label>
						</div>
					</div>

					<div class="form-group">
						<div class="form-check">
							<input name="hide_from_main_page" type="hidden" value="0"/>
							<input id="hide_from_main_page_check" name="hide_from_main_page" type="checkbox"
								   class="form-check-input {{ $errors->has('hide_from_main_page') ? ' is-invalid' : '' }}"
								   @if (old('hide_from_main_page')) checked="checked" @endif
								   value="1"/>
							<label class="form-check-label" for="hide_from_main_page_check">
								{{ __('topic.hide_from_main_page') }}
							</label>
						</div>
					</div>

				@endcan

				@include('ckeditor')

				<div class="form-group">
					<label for="bb_text" class="col-form-label">{{ __('common.your_message') }}</label>
					<textarea id="bb_text" name="bb_text"
							  class="form-control sceditor {{ $errors->has('bb_text') ? ' is-invalid' : '' }}"
							  rows="{{ config('litlife.textarea_rows') }}">{{ old('bb_text') }}</textarea>
					@foreach ($errors->get('bb_text') as $error)
						<div class="invalid-feedback">{{ $error }}</div>
					@endforeach
				</div>

				<button type="submit" class="btn btn-primary">{{ __('common.create') }}</button>

			</form>
		</div>
	</div>
@endsection