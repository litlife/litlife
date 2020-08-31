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

			<form role="form" action="{{ route('forum_groups.update', $forumGroup) }}"
				  method="post" enctype="multipart/form-data">

				@csrf
				@method('patch')

				<div class="row form-group{{ $errors->has('image') ? ' has-error' : '' }}">
					<label for="image" class="col-md-3 col-lg-2 col-form-label">{{ __('forum_group.image') }}</label>
					<div class="col-md-9 col-lg-10 d-flex flex-row">
						@include('forum.group.icon')
						<input name="image" type="file" class="form-control-file" id="image">
					</div>
				</div>

				<div class="row form-group{{ $errors->has('name') ? ' has-error' : '' }}">
					{{ Form::label('name', __('forum_group.name').' ', ['class' => 'col-md-3 col-lg-2 col-form-label']) }}
					<div class="col-md-9 col-lg-10">
						{{ Form::text('name', old('name') ?: $forumGroup->name, ['class' => 'form-control']) }}
					</div>
				</div>

				<div class="row form-group">
					<div class="col-md-9 col-lg-10 offset-md-3 offset-lg-2">
						<button type="submit" class="btn btn-primary">{{ __('common.save') }}</button>
					</div>
				</div>

			</form>
		</div>
	</div>
@endsection