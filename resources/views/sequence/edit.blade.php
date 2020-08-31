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


			<form role="form" action="{{ route('sequences.update', $sequence) }}"
				  method="post" enctype="multipart/form-data">

				@csrf
				@method('patch')


				<div class="row form-group{{ $errors->has('name') ? ' has-error' : '' }}">
					<label for="name" class="col-md-3 col-lg-2 col-form-label">{{ __('sequence.name') }}</label>
					<div class="col-md-9 col-lg-10">
						{{ Form::text('name', old('name') ?: $sequence->name, ['class' => 'form-control']) }}
					</div>
				</div>

				<div class="row form-group{{ $errors->has('description') ? ' has-error' : '' }}">
					<label for="description"
						   class="col-md-3 col-lg-2 col-form-label">{{ __('sequence.description') }}</label>
					<div class="col-md-9 col-lg-10">
                        <textarea id="description" class="editor form-control" rows="5"
								  name="description">{{ old('description') ?? $sequence->description  }}</textarea>
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
