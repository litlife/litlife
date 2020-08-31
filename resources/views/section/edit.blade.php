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

			<form role="form" method="POST"
				  action="{{ route('books.sections.update', ['book' => $book, 'section' => $section->inner_id]) }}">

				@csrf

				@method('patch')

				@if (session('success'))
					<div class="alert alert-success alert-dismissable">
						{{ session('success') }}
						<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
					</div>
				@endif

				<div class="form-group">
					<input name="title" class="form-control {{ $errors->has('title') ? ' is-invalid' : '' }}" type="text"
						   placeholder="{{ __('section.title') }}"
						   value="{{ old('title') ?? $section->title ?? ''  }}">
				</div>

				<div class="form-group">

					@if(($section->_rgt-$section->_lft) > 1)
						<label for="content" class="col-form-label">{{ __('section.annotation_and_epigraphs') }}</label>
					@else
						<label for="content" class="col-form-label">{{ __('section.content') }}</label>
					@endif

					@include('ckeditor_book', ['book' => $book])

					<textarea id="content" name="content"
							  class="ckeditor_book">{{ old('content') ?? $section->getContent() ?? ''  }}</textarea>
				</div>

				@can ('use_draft', $section)

					<fieldset class="form-group">
						<div class="form-check">
							<input class="form-check-input" type="radio" name="status" id="gridRadios1"
								   value="{{ \App\Enums\StatusEnum::Accepted }}"
								   @if ($section->isAccepted()) checked @endif>
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
								   @if ($section->isPrivate()) checked @endif>
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