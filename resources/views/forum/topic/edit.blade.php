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

			<form role="form" action="{{ route('topics.update', compact('topic')) }}" method="post">

				@csrf
				@method('patch')

				<div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
					{{ Form::label('name', __('topic.name').' ', ['class' => 'col-form-label']) }}
					{{ Form::text('name', old('name') ?: $topic->name, ['class' => 'form-control']) }}
				</div>

				<div class="form-group{{ $errors->has('description') ? ' has-error' : '' }}">
					<label for="description" class="col-form-label">
						{{ __('topic.description') }}
					</label>

					<textarea id="description" class="form-control" rows="3"
							  name="description">{{ old('description') ?? $topic->description  }}</textarea>
				</div>

				@can ('edit_spectial_settings', \App\Topic::class)

					<div class="form-group{{ $errors->has('forum_priority') ? ' has-error' : '' }}">
						{{ Form::label('forum_priority', __('topic.forum_priority').' ', ['class' => 'col-form-label']) }}
						{{ Form::text('forum_priority', old('forum_priority') ?: $topic->forum_priority, ['class' => 'form-control']) }}
					</div>

					<div class="form-group{{ $errors->has('main_priority') ? ' has-error' : '' }}">
						{{ Form::label('main_priority', __('topic.main_priority').' ', ['class' => 'col-form-label']) }}
						{{ Form::text('main_priority', old('main_priority') ?: $topic->main_priority, ['class' => 'form-control']) }}
					</div>

					<div class="form-group">
						<div class="form-check">
							<input name="post_desc" type="hidden" value="0"/>
							<input id="post_desc_check" name="post_desc" type="checkbox"
								   class="form-check-input {{ $errors->has('post_desc') ? ' is-invalid' : '' }}"
								   @if (old('post_desc') ?: $topic->post_desc) checked="checked" @endif
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
								   @if (old('hide_from_main_page') ?: $topic->hide_from_main_page) checked="checked" @endif
								   value="1"/>
							<label class="form-check-label" for="hide_from_main_page_check">
								{{ __('topic.hide_from_main_page') }}
							</label>
						</div>
					</div>


					<div class="form-group">
						<label for="label" class="col-form-label">
							{{ __('topic.label') }}
						</label>
						<select id="label" name="label"
								class="form-control{{ $errors->user->has('label') ? ' is-invalid' : '' }}">
							<option value=""> -</option>
							@foreach (\App\Enums\TopicLabelEnum::asArray() as $key => $value)
								@if ($value == (old('label') ?: $topic->label))
									<option value="{{ $value }}" selected>{{ __('topic.labels.'.$key) }}</option>
								@else
									<option value="{{ $value }}">{{ __('topic.labels.'.$key) }}</option>
								@endif
							@endforeach
						</select>

					</div>

				@endcan

				<button type="submit" class="btn btn-primary">{{ __('common.save') }}</button>

			</form>
		</div>
	</div>
@endsection