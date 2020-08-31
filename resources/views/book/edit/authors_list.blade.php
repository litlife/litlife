<div class="row form-group {{ $type }}">

	@if ($type == 'writers')
		<label for="writers" class="col-md-3 col-lg-2 col-form-label">
			{{ trans_choice('book.writers', 2) }} *
		</label>
	@elseif ($type == 'translators')
		<label for="translators" class="col-md-3 col-lg-2 col-form-label">
			{{ trans_choice('book.translators', 2) }}
		</label>
	@endif

	<div class="col-md-9 col-lg-10">

		@if ($type == 'writers')

			<select id="writers" name="writers[]" multiple
					@cannot('changeWritersField', $book) readonly @endcannot
					class="author-select form-control{{ $errors->has('writers') ? ' is-invalid' : '' }}">
				@foreach(old('writers') ? App\Author::whereIn('id', old('writers'))->orderByField('id', old('writers'))->get() : $book->writers as $c => $author)
					<option value="{{ $author->id }}" selected>{{ $author->fullName }}</option>
				@endforeach
			</select>
			<small class="form-text text-muted">
				{{ __('author.to_search_start_entering_the_writers_name_if_the_search_did_not_yield_results_then_create_a_new_page_for_the_writer') }}
			</small>
			<a class="btn btn-outline-secondary" href="{{ route('authors.create') }}" target="_blank"
			   style="margin-top:5px;">
				{{ __('author.create') }}
			</a>
			<small class="form-text text-muted">
				{{ __('author.once_you_have_created_a_page_it_will_be_available_in_the_search') }}
			</small>

		@elseif ($type == 'translators')

			<select id="translators" name="translators[]" multiple
					class="author-select form-control{{ $errors->has('translators') ? ' is-invalid' : '' }}">
				@foreach(old('translators') ? App\Author::whereIn('id', old('translators'))->orderByField('id', old('translators'))->get() : $book->translators as $c => $author)
					<option value="{{ $author->id }}" selected>{{ $author->fullName }}</option>
				@endforeach
			</select>

			<small class="form-text text-muted">
				{{ __('author.to_search_start_entering_the_translator_name_if_the_search_did_not_yield_results_then_create_a_new_page_for_the_writer') }}
			</small>
			<a class="btn btn-outline-secondary" href="{{ route('authors.create') }}" target="_blank"
			   style="margin-top:5px;">
				{{ __('translator.create') }}
			</a>
			<small class="form-text text-muted">
				{{ __('author.once_you_have_created_a_page_it_will_be_available_in_the_search') }}
			</small>
		@endif

	</div>
</div>
