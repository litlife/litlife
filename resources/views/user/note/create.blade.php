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


			<form role="form" method="POST" action="{{ route('users.notes.store', ['user' => $user]) }}"
				  enctype="multipart/form-data">
				@csrf

				<div class="form-group{{ $errors->has('bb_text') ? ' has-error' : '' }}">
					<label class="col-form-label" for="bb_text">{{ __('user_note.bb_text') }}</label>

					<textarea id="bb_text" class="sceditor form-control{{ $errors->has('bb_text') ? ' is-invalid' : '' }}"
							  rows="{{ config('litlife.textarea_rows') }}" name="bb_text">{{ old('bb_text') }}</textarea>
				</div>
				<button type="submit" class="btn btn-primary">{{ __('common.create') }}</button>
			</form>
		</div>
	</div>

@endsection
