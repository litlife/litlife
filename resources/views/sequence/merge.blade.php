@extends('layouts.app')

@section('content')

	@if (session('success'))
		<div class="alert alert-success alert-dismissable">
			{{ session('success') }}
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		</div>
	@endif

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

			<form role="form" method="POST"
				  action="{{ route('sequences.merge', compact('sequence')) }}" enctype="multipart/form-data">

				@csrf

				<div class="form-group{{ $errors->has('merged_to_sequence_id') ? ' has-error' : '' }}">
					<label for="merged_to_sequence_id"
						   class="col-form-label">{{ __('sequence.merged_to_sequence_id') }}</label>

					<input id="merged_to_sequence_id" class="form-control" name="merged_to_sequence_id"
						   value="{{ old('merged_to_sequence_id') }}"/>

				</div>

				<div class="form-group">

					<button type="submit" class="btn btn-primary" aria-describedby="mergeHelper">
						{{ __('common.merge') }}
					</button>

					<small id="mergeHelper" class="form-text text-muted">
						{{ __('sequence.merge_helper') }}
					</small>

				</div>

			</form>

		</div>
	</div>
@endsection
