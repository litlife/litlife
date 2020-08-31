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

	@if (session('success'))
		<div class="alert alert-success alert-dismissable">
			{!! session('success') !!}
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		</div>
	@endif

	<div class="card">
		<div class="card-body">

			<form role="form" action="{{ route('topics.move', compact('topic')) }}"
				  method="post" enctype="multipart/form-data">

				@csrf


				<div class="form-group{{ $errors->has('forum') ? ' has-error' : '' }}">
					{{ Form::label('forum', __('forum.id'), ['class' => 'col-form-label']) }}

					{{ Form::text('forum', old('forum') ?: '', ['class' => 'form-control']) }}

				</div>

				<button type="submit" class="btn btn-primary">{{ __('common.move') }}</button>

			</form>
		</div>
	</div>

@endsection