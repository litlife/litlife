@extends('layouts.app')

@section('content')

	@if ($errors->any())
		<div class="alert alert-danger">
			<ul>
				@foreach ($errors->all() as $error)
					<li>{{ $error }}</li>
				@endforeach
			</ul>
		</div>
	@endif

	<div class="card">
		<div class="card-body">
			<form role="form" method="POST" action="{{ route('admin_notes.update', compact('admin_note')) }}">

				@method('patch')

				@csrf


				<div class="form-group">
					<label for="text" class="col-form-label">{{ __('admin_note.text') }}</label>

					<textarea id="text" class="form-control {{ $errors->has('text') ? ' is-invalid' : '' }}"
							  name="text" rows="3">{{ old('text') ?? $admin_note->text ?? ''  }}</textarea>
				</div>

				<button type="submit" class="btn btn-primary">
					{{ __('common.save') }}
				</button>

			</form>
		</div>
		<div class="card-footer">
			<small class="text-muted">
				@if (!empty($admin_note->create_user))
					{{ trans_choice('user.edited', $admin_note->create_user->gender) }}:
					<x-user-name :user="$admin_note->create_user"/>
				@endif

				<x-time :time="optional($admin_note)->user_edited_at ?? null"/>
			</small>
		</div>
	</div>

@endsection