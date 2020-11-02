<div class="card mb-2">
	<div class="card-header">
		{{ __('Share your opinion about the work of support') }}
	</div>
	<div class="card-body">

		<form role="form" action="{{ route('support_questions.feedbacks.store', $supportQuestion) }}" method="post">

			@csrf

			<div class="mb-3">

				<div class="btn-group btn-group-toggle" data-toggle="buttons">

					@foreach (\App\Enums\FaceReactionEnum::asSelectArray() as $key => $value)
						<label class="btn btn-light  btn-lg">
							<input type="radio" name="face_reaction" id="face_reaction_{{ $value }}" value="{{ $key }}" autocomplete="off">

							@switch($value)
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
						</label>

					@endforeach
				</div>
			</div>

			<div class="form-group">
		<textarea id="text" name="text"
				  class="form-control {{ $errors->has('text') ? ' is-invalid' : '' }}"
				  rows="2" placeholder="{{ __('Text of your review') }}">{{ old('text') }}</textarea>
			</div>

			<button type="submit" class="btn btn-primary">{{ __('Send') }}</button>

		</form>
	</div>
</div>