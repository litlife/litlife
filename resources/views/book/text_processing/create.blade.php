@extends('layouts.app')


@section('content')

	@include ('book.edit_tab')

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

	<div class="card">
		<div class="card-body">

			<form role="form" method="POST" action="{{ route('books.text_processings.store', $book) }}">
				@csrf

				<div class="form-group form-check">
					<input name="remove_bold" type="hidden" value="0">
					<input name="remove_bold" class="form-check-input" type="checkbox" value="1" id="remove_bold">
					<label class="form-check-label" for="remove_bold">
						{{ __('book_text_processing.remove_bold') }}
					</label>
				</div>

				<div class="form-group form-check">
					<input name="remove_italics" type="hidden" value="0">
					<input name="remove_italics" class="form-check-input" type="checkbox" value="1" id="remove_italics">
					<label class="form-check-label" for="remove_italics">
						{{ __('book_text_processing.remove_italics') }}
					</label>
				</div>

				<div class="form-group form-check">
					<input name="remove_extra_spaces" type="hidden" value="0">
					<input name="remove_extra_spaces" class="form-check-input" type="checkbox" value="1" id="remove_extra_spaces">
					<label class="form-check-label" for="remove_extra_spaces">
						{{ __('book_text_processing.remove_extra_spaces') }}
					</label>
				</div>

				<div class="form-group form-check">
					<input name="split_into_chapters" type="hidden" value="0">
					<input name="split_into_chapters" class="form-check-input" type="checkbox" value="1" id="split_into_chapters">
					<label class="form-check-label" for="split_into_chapters">
						{{ __('book_text_processing.split_into_chapters') }}
					</label>
				</div>

				<div class="form-group form-check">
					<input name="convert_new_lines_to_paragraphs" type="hidden" value="0">
					<input name="convert_new_lines_to_paragraphs" class="form-check-input" type="checkbox" value="1" id="convert_new_lines_to_paragraphs">
					<label class="form-check-label" for="convert_new_lines_to_paragraphs">
						{{ __('book_text_processing.convert_new_lines_to_paragraphs') }}
					</label>
				</div>

				<div class="form-group form-check">
					<input name="add_a_space_after_the_first_hyphen_in_the_paragraph" type="hidden" value="0">
					<input name="add_a_space_after_the_first_hyphen_in_the_paragraph" class="form-check-input" type="checkbox" value="1"
						   id="add_a_space_after_the_first_hyphen_in_the_paragraph">
					<label class="form-check-label" for="add_a_space_after_the_first_hyphen_in_the_paragraph">
						{{ __('book_text_processing.add_a_space_after_the_first_hyphen_in_the_paragraph') }}
					</label>
				</div>

				<div class="form-group form-check">
					<input name="remove_spaces_before_punctuations_marks" type="hidden" value="0">
					<input name="remove_spaces_before_punctuations_marks" class="form-check-input" type="checkbox" value="1"
						   id="remove_spaces_before_punctuations_marks">
					<label class="form-check-label" for="remove_spaces_before_punctuations_marks">
						{{ __('book_text_processing.remove_spaces_before_punctuations_marks') }}
					</label>
				</div>

				<div class="form-group form-check">
					<input name="add_spaces_after_punctuations_marks" type="hidden" value="0">
					<input name="add_spaces_after_punctuations_marks" class="form-check-input" type="checkbox" value="1"
						   id="add_spaces_after_punctuations_marks">
					<label class="form-check-label" for="add_spaces_after_punctuations_marks">
						{{ __('book_text_processing.add_spaces_after_punctuations_marks') }}
					</label>
				</div>

				<div class="form-group form-check">
					<input name="merge_paragraphs_if_there_is_no_dot_at_the_end" type="hidden" value="0">
					<input name="merge_paragraphs_if_there_is_no_dot_at_the_end" class="form-check-input" type="checkbox" value="1"
						   id="merge_paragraphs_if_there_is_no_dot_at_the_end">
					<label class="form-check-label" for="merge_paragraphs_if_there_is_no_dot_at_the_end">
						{{ __('book_text_processing.merge_paragraphs_if_there_is_no_dot_at_the_end') }}
					</label>
				</div>

				<div class="form-group form-check">
					<input name="tidy_chapter_names" type="hidden" value="0">
					<input name="tidy_chapter_names" class="form-check-input" type="checkbox" value="1"
						   id="tidy_chapter_names">
					<label class="form-check-label" for="tidy_chapter_names">
						{{ __('book_text_processing.tidy_chapter_names') }}
					</label>
				</div>

				<div class="form-group form-check">
					<input name="remove_empty_paragraphs" type="hidden" value="0">
					<input name="remove_empty_paragraphs" class="form-check-input" type="checkbox" value="1"
						   id="remove_empty_paragraphs">
					<label class="form-check-label" for="remove_empty_paragraphs">
						{{ __('book_text_processing.remove_empty_paragraphs') }}
					</label>
				</div>

				<button type="submit" class="btn btn-primary">
					{{ __('book_text_processing.processing') }}
				</button>

			</form>

		</div>
	</div>

@endsection
