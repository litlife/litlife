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

			<form role="form" method="POST" action="{{ $action }}">

				@csrf

				@if($section->isSection() and isset($parent->inner_id))
					{{ Form::hidden('parent', $parent->inner_id) }}
				@endif

				<div class="form-group">
					<input id="title" name="title" class="form-control {{ $errors->has('title') ? ' is-invalid' : '' }}"
						   type="text"
						   placeholder="{{ __('section.title') }}"
						   value="{{ old('title') ?? ''  }}">
				</div>

				<div class="form-group">
					<textarea id="content" name="content" class="ckeditor_book">{{ old('content') ?? ''  }}</textarea>
					@include('ckeditor_book', ['book' => $book])
				</div>

				@can ('use_draft', $section)

					<fieldset class="form-group">
						<div class="form-check">
							<input class="form-check-input" type="radio" name="status" id="gridRadios1"
								   value="{{ \App\Enums\StatusEnum::Accepted }}">
							<label class="form-check-label" for="gridRadios1">
								{{ __('section.status_array.accepted') }}
							</label>
							<small id="gridRadios1Help" class="form-text text-muted">
								{{ __('section.status_array_helper.accepted') }}
							</small>
						</div>
						<div class="form-check">
							<input class="form-check-input" type="radio" name="status" id="gridRadios2"
								   value="{{ \App\Enums\StatusEnum::Private }}"
								   checked>
							<label class="form-check-label" for="gridRadios2">
								{{ __('section.status_array.private') }}
							</label>
							<small id="gridRadios2Help" class="form-text text-muted">
								{{ __('section.status_array_helper.private') }}
							</small>
						</div>
					</fieldset>

				@endcan

				<div class="form-group">
					<button type="submit" class="btn btn-primary">
						{{ __('common.save') }}
					</button>
				</div>

			</form>
		</div>
	</div>

@endsection