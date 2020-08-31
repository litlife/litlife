@extends('layouts.app')

@section('content')

	<div class="card">
		<div class="card-body">

			<form role="form" action="{{ route('genres.update', compact('genre')) }}"
				  method="post" enctype="multipart/form-data">

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

				<div class="row form-group">
					{{ Form::label('name', __('genre.name').' ', ['class' => 'col-md-3 col-lg-2 col-form-label']) }}
					<div class="col-md-9 col-lg-10">
						{{ Form::text('name', old('name') ?? $genre->name, ['class' => 'form-control']) }}

						@if ($errors->has('name'))
							<p class="help-block">{{ $errors->first('name') }}</p>
						@endif
					</div>
				</div>

				<div class="row form-group">
					{{ Form::label('fb_code', __('genre.fb_code').' ', ['class' => 'col-md-3 col-lg-2 col-form-label']) }}
					<div class="col-md-9 col-lg-10">
						{{ Form::text('fb_code', old('fb_code') ?? $genre->fb_code, ['class' => 'form-control']) }}

						@if ($errors->has('fb_code'))
							<p class="help-block">{{ $errors->first('fb_code') }}</p>
						@endif
					</div>
				</div>

				<div class="row form-group">
					{{ Form::label('age', __('genre.age').' ', ['class' => 'col-md-3 col-lg-2 col-form-label']) }}
					<div class="col-md-9 col-lg-10">
						{{ Form::text('age', old('age') ?? $genre->age, ['class' => 'form-control']) }}

						@if ($errors->has('age'))
							<p class="help-block">{{ $errors->first('age') }}</p>
						@endif
					</div>
				</div>

				<div class="row form-group">
					{{ Form::label('genre_group_id', __('genre.genre_group_id').' ', ['class' => 'col-md-3 col-lg-2 col-form-label']) }}
					<div class="col-md-9 col-lg-10">
						{{ Form::text('genre_group_id', old('genre_group_id') ?? $genre->genre_group_id, ['class' => 'form-control']) }}

						@if ($errors->has('genre_group_id'))
							<p class="help-block">{{ $errors->first('genre_group_id') }}</p>
						@endif
					</div>
				</div>

				<div class="row form-group">
					<div class="col-12 offset-md-2">
						<button type="submit" class="btn btn-primary">{{ __('common.save') }}</button>
					</div>
				</div>

			</form>

		</div>
	</div>

@endsection