<div class="card mb-2 support_question">
	<div class="card-header d-flex">
		<div class="flex-grow-1 d-flex flex-row align-items-center">
			<div class="mr-2" style="min-width: 30px; max-width: 30px;">
				<x-user-avatar :user="$item->create_user" width="30" height="30"/>
			</div>
			<x-user-name :user="$item->create_user"/>
		</div>
		<div class="flex-shrink-1 small text-nowrap d-flex flex-row align-items-center">
			{{ __('Messages') }}:
			{{ $item->number_of_messages }}
		</div>
	</div>
	<div class="card-body">

		<h6 class="card-title">
			<a href="{{ route('support_questions.edit', $item) }}" target="_blank">
				{{ __(\App\Enums\SupportQuestionTypeEnum::getDescription($item->category)) }}</a>:

			<a href="{{ route('support_questions.show', $item) }}">
				{{ $item->title }}
			</a>
		</h6>

		<div class="status">
			@include('support_question.status')
		</div>

		@if ($item->hasFeedback())
			<div class="d-flex mt-3">

				<a href="#" data-toggle="collapse" data-target="#collapse_{{ $item->id }}"
				   data-toggle="tooltip" data-placement="top" title="{{ __('Click to expand the review') }}"
				   class="text-decoration-none mr-2"
				   aria-expanded="false" aria-controls="collapseExample">
					@switch(\App\Enums\FaceReactionEnum::getKey($item->feedback->face_reaction))
						@case('Smile')
						<i class="far fa-smile h3 mb-0 text-primary"></i>
						@break
						@case('Meh')
						<i class="far fa-meh h3 mb-0 text-primary"></i>
						@break
						@case('Sad')
						<i class="far fa-frown h3 mb-0 text-primary"></i>
						@break
					@endswitch
				</a>

				<div class="collapse" id="collapse_{{ $item->id }}">
					{{ $item->feedback->text }}
				</div>
			</div>
		@endif

	</div>

	<div class="card-footer buttons">

		<a class="btn-continue-reviewing btn btn-primary" href="{{ route('support_questions.show', $item) }}"
		   @cannot ('continueReviewing', $item) style="display:none;" @endcannot>
			{{ __('Сontinue reviewing') }}
		</a>

		<a class="btn-start-review btn btn-primary" href="{{ route('support_questions.start_review', $item) }}"
		   @cannot ('startReview', $item) style="display:none;" @endcannot>
			{{ __('Start reviewing') }}
		</a>

		<a class="btn btn-approve btn-outline-success" href="{{ route('support_questions.solve', $item) }}"
		   @cannot ('solve', $item) style="display:none;" @endcannot>
			<i class="far fa-check-circle"></i> {{ __('Mark as resolved') }}
		</a>

		<a class="btn-stop-review btn btn-outline-danger"
		   href="{{ route('support_questions.stop_review', $item) }}"
		   @cannot ('stopReview', $item) style="display:none;" @endcannot>
			<i class="far fa-times-circle"></i> {{ __('Decline to review') }}
		</a>

	</div>
</div>