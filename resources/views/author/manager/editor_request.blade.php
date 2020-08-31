@extends('layouts.app')

@section('content')

	@isset($manager)
		@if ($manager->isRejected())
			<div class="row">
				<div class="col-12">
					<div class="alert alert-info">
						{{ __('manager.declined') }}
					</div>
				</div>
			</div>
		@elseif ($manager->isSentForReview())
			<div class="row">
				<div class="col-12">
					<div class="alert alert-info">
						{{ trans_choice('manager.on_check', 1) }}
					</div>
				</div>
			</div>

		@elseif ($manager->isAccepted())
			<div class="row">
				<div class="col-12">
					<div class="alert alert-success">
						{{ __('manager.succeed') }}
					</div>
				</div>
			</div>
		@endif
	@endif

	@include('text_block.item', ['name' => 'Заявка на редактирование автора'])

	@if ($errors->any())
		<div class="alert alert-danger">
			<ul class="mb-0">
				@foreach ($errors->all() as $error)
					<li>{{ $error }}</li>
				@endforeach
			</ul>
		</div>
	@endif

	<div class="card">
		<div class="card-body">

			<form action="{{ route('authors.editor.request_save', $author) }}" method="post">

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


@endsection