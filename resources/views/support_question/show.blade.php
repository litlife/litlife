@extends('layouts.app')

@push('scripts')

@endpush

@push('css')

@endpush

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
			{{ session('success') }}
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		</div>
	@endif

	@if ($supportQuestion->feedback)
		<div class="card mb-2">
			<div class="card-body flex-row d-flex">
				<div class="mr-3">
					@switch(\App\Enums\FaceReactionEnum::getKey($supportQuestion->feedback->face_reaction))
						@case('Smile')
						<i class="far fa-smile h1 mb-0 text-primary"></i>
						@break
						@case('Meh')
						<i class="far fa-meh h1 mb-0 text-primary"></i>
						@break
						@case('Sad')
						<i class="far fa-frown h1 mb-0 text-primary"></i>
						@break
					@endswitch
				</div>

				<div class="">
					{{ $supportQuestion->feedback->text }}
				</div>
			</div>
		</div>
	@else
		@can('create_feedback', $supportQuestion)
			@include('support_question.feedback.form')
		@endcan
	@endif

	@can('createMessage', $supportQuestion)
		<div class="card mb-2">
			<div class="card-body">
				@include('support_question.message.form')
			</div>
		</div>
	@endcan

	@if ($supportQuestion->isAuthUserCreator())
		@can ('solve', $supportQuestion)
			<div class="alert alert-info mb-2">
				{{ __('Is your question resolved? If Yes, please click here:') }}
				<a href="{{ route('support_questions.solve', $supportQuestion) }}" class="alert-link">
					{{ __('My question is resolved') }}
				</a>
			</div>
		@endcan
	@else
		@if ($supportQuestion->isSentForReview())
			<div class="alert alert-info">
				{{ __("To answer you need to start reviewing the question") }}.
				@can ('startReview', $supportQuestion)
					<a class="alert-link" href="{{ route('support_questions.start_review', $supportQuestion) }}">{{ __('Start reviewing') }}</a>
				@endcan
			</div>
		@endif
	@endif

	@if(!empty($messages) and count($messages) > 0)

		@if ($messages->hasPages())
			<div class="row mt-3">
				<div class="col-12">
					{{ $messages->appends(request()->except(['page', 'ajax']))->links() }}
				</div>
			</div>
		@endif

		@foreach ($messages as $item)

			@include('support_question.message.item')

		@endforeach

		@if ($messages->hasPages())
			<div class="row mt-3">
				<div class="col-12">
					{{ $messages->appends(request()->except(['page', 'ajax']))->links() }}
				</div>
			</div>
		@endif

	@else
		<div class="row mt-3">
			<div class="col-12">
				<div class="alert alert-info">{{ __('No messages found') }}</div>
			</div>
		</div>
	@endif

@endsection