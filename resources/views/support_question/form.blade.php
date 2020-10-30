<form role="form" action="{{ route('support_questions.store', ['user' => $user, 'support_question' => $supportQuestion->id ?? null]) }}" method="post">

	@csrf

	<div class="form-group">
		<select id="category" name="category" class="form-control {{ $errors->has('category') ? ' is-invalid' : '' }}">
			<option value="" disabled selected>{{ __('Select a category') }}</option>
			@foreach (\App\Enums\SupportQuestionTypeEnum::asSelectArray() as $key => $value)
				<option value="{{ $key }}" @if (old('category') == $key) selected @endif>{{ __($value) }}</option>
			@endforeach
		</select>
	</div>

	<div class="form-group">
		<input id="title" name="title"
			   class="form-control {{ $errors->has('title') ? ' is-invalid' : '' }}" value="{{ old('title') }}"
			   placeholder="{{ __('support_question.title') }}"/>
	</div>

	<div class="form-group">
		<textarea id="bb_text" name="bb_text"
				  class="sceditor form-control {{ $errors->has('bb_text') ? ' is-invalid' : '' }}"
				  rows="{{ config('litlife.textarea_rows') }}" placeholder="{{ __('support_question.bb_text') }}">{{ old('bb_text') }}</textarea>
	</div>

	<button type="submit" class="btn btn-primary">{{ __('Send') }}</button>

</form>