@extends('layouts.app')

@push('scripts')

@endpush

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

			<form role="form" action="{{ route('notes.update', $note) }}"
				  method="post" enctype="multipart/form-data">

				@csrf
				@method('patch')

				<div class="form-group{{ $errors->has('bb_text') ? ' has-error' : '' }}">

					<label for="bb_text" class="col-form-label">{{ __('user_note.bb_text') }}</label>

					<textarea id="bb_text" class="sceditor form-control"
							  rows="{{ config('litlife.textarea_rows') }}"
							  name="bb_text">{{ old('bb_text') ?? $note->bb_text }}</textarea>
				</div>

				<button type="submit" class="btn btn-primary">{{ __('common.save') }}</button>

			</form>
		</div>
	</div>
@endsection
