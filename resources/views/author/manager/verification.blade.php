@extends('layouts.app')

@section('content')

	@include('text_block.item', ['name' => 'Заявка на верификацию автора'])

	@if ($errors->any())
		<div class="alert alert-danger">
			<ul class="mb-0">
				@foreach ($errors->all() as $error)
					<li>{{ $error }}</li>
				@endforeach
			</ul>
		</div>
	@endif

	@can('verficationRequest', $author)

		<div class="card">
			<div class="card-body">

				<form action="{{ route('authors.verification.request_save', $author) }}" method="post">

					@csrf

					@if (session('success'))
						<div class="alert alert-success alert-dismissable">
							{{ session('success') }}
							<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
						</div>
					@endif

					@include('ckeditor')

					<div class="form-group">
						<label for="comment" class="col-form-label">{{ __('manager.comment') }}</label>:
						<textarea id="comment" name="comment"
								  class="form-control{{ $errors->has('comment') ? ' is-invalid' : '' }}"
								  rows="{{ config('litlife.textarea_rows') }}">{{ old('comment') || isset($manager->comment) ? $manager->comment : ''  }}</textarea>
					</div>

					<button type="submit" class="btn btn-primary">{{ __('manager.send_request') }}</button>

				</form>
			</div>
		</div>

	@endcan


@endsection