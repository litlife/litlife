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


			<form role="form" action="{{ route('admin_notes.store', ['type' => $type, 'id' => $admin_noteable->id]) }}"
				  method="post" enctype="multipart/form-data">

				@csrf


				<div class="form-group">

					<label for="text" class="col-form-label">{{ __('admin_note.text') }}</label>
					<textarea id="ckeditor" class="form-control{{ $errors->has('text') ? ' is-invalid' : '' }}" rows="5"
							  name="text">{{ old('text') }}</textarea>
				</div>

				<button type="submit" class="btn btn-primary">{{ __('common.save') }}</button>

			</form>
		</div>
	</div>

@endsection
