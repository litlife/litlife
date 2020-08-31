@extends('layouts.app')

@section('content')

	<div class="row">
		<div class="col-12">
			@include('text_block.item', ['name' => 'paid_book_publishing_rules'])
		</div>
	</div>

	@if ($completeBooksCount < 1)
		<div class="alert alert-warning">
			{{ __('author_sale_request.to_send_a_request_the_author_must_have_at_least_one_finished_book') }}
		</div>
	@endif

	@if (!$authorHasBooksAddedByAuthUser)
		<div class="alert alert-warning">
			{{ __('author_sale_request.your_author_page_must_have_at_least_one_book_added_by_you') }}
		</div>
	@endif

	@if (!$isEnoughBooksTextCharacters)
		<div class="alert alert-warning">
			{{ __('author_sale_request.to_submit_a_request_your_added_books_must_have_at_least_two_characters_of_text_in_total', ['characters_count' => config('litlife.the_total_number_of_characters_of_the_authors_books_in_order_to_be_allowed_to_send_a_request_for_permission_to_sell_books')]) }}
		</div>
	@endif

	@if ($errors->any())
		<div class="alert alert-danger">
			<ul class="mb-0">
				@foreach ($errors->all() as $error)
					<li>{!! $error !!}</li>
				@endforeach
			</ul>
		</div>
	@endif

	<div class="card">
		<div class="card-body">

			<form action="{{ route('authors.sales.store', $author) }}" method="post">

				@csrf

				@if (session('success'))
					<div class="alert alert-success alert-dismissable">
						{{ session('success') }}
						<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
					</div>
				@endif

				@include('ckeditor')

				<div class="form-group">
					<label for="text" class="col-form-label">{{ __('author_sale_request.text') }}</label>:
					<textarea id="text" name="text"
							  class="form-control{{ $errors->has('text') ? ' is-invalid' : '' }}"
							  rows="{{ config('litlife.textarea_rows') }}">{{ old('text') }}</textarea>
				</div>

				<div class="form-group form-check">
					<input type="hidden" value="0" name="rules_accepted"/>
					<input name="rules_accepted" type="checkbox"
						   class="form-check-input{{ $errors->has('rules_accepted') ? ' is-invalid' : '' }}"
						   id="rules_accepted" value="1" {{ old('rules_accepted') ? 'checked' : '' }}>
					<label class="form-check-label"
						   for="rules_accepted">{!! __('author_sale_request.rules_accepted') !!}</label>
				</div>

				<button type="submit" class="btn btn-primary">{{ __('author_sale_request.save_request') }}</button>

			</form>
		</div>
	</div>

@endsection