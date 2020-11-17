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

	@if (session('success'))
		<div class="alert alert-success alert-dismissable">
			{{ session('success') }}
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		</div>
	@endif

	<div class="card">
		<div class="card-body">
			<form role="form" action="{{ route('support_questions.update', ['support_question' => $supportQuestion]) }}"
				  method="post">

				@csrf
				@method('patch')

				<div class="form-group">
					<select id="category" name="category" class="form-control {{ $errors->has('category') ? ' is-invalid' : '' }}">
						<option value="" disabled selected>{{ __('Select a category') }}</option>
						@foreach (\App\Enums\SupportQuestionTypeEnum::asSelectArray() as $key => $value)
							<option value="{{ $key }}" @if (old('category') == $key or $supportQuestion->category == $key) selected @endif>
								{{ __($value) }}</option>
						@endforeach
					</select>
				</div>

				<button type="submit" class="btn btn-primary">{{ __('Save') }}</button>

			</form>
		</div>
	</div>

@endsection