@extends('layouts.app')

@push('scripts')

@endpush

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

	@if ($book->isRejected())
		<div class="alert alert-warning">
			{{ __('book.removed_from_sale') }}
		</div>
	@else

		@if (!$manager->can_sale)
			<div class="alert alert-warning">
				<p>{{ __('book.to_sell_books_you_need_to_request') }}</p>
				<a href="{{ route('authors.sales.request', ['author' => $manager->manageable])  }}"
				   class="btn btn-primary">{{ __('book.sent_request') }}</a>
			</div>
		@elseif (!$seller)
			<div class="alert alert-danger">
				{{ __('book.you_must_specify_your_author_page_in_the_writers_field') }}
			</div>
		@else

			@if ($book->is_lp)
				<div class="alert alert-warning">
					{{ __('book.amateur_translations_cannot_be_sold') }}
				</div>
			@endif

			@if (!$book->isForSale())
				@if ($book->getAuthorsWithType('writers')->count() > 1)
					<div class="alert alert-warning">
						{{ __('book.we_dont_have_the_opportunity_to_sell_books_with_more_than_one_writer') }}
					</div>
				@endif
			@endif

			@if ($book->isForSale() and !$book->isReadOrDownloadAccess())
				<div class="alert alert-warning">
					{{ __('book.for_the_book_to_start_being_sold_you_need_to_allow_access_to_reading_or_downloading') }}
				</div>
			@endif

			@if ($isChapterWithExceedingTheNumberOfCharactersExists)
				<div class="alert alert-warning">
					<p>{{ __('book.book_should_be_divided_into_chapters_and_parts', ['max_symbols_count' => config('litlife.max_section_characters_count')]) }}</p>
				</div>
			@endif

			@if (!$book->isForSale())

				@if ($book->ready_status == 'complete_but_publish_only_part' or $book->ready_status == 'not_complete_and_not_will_be')
					<div class="alert alert-warning">
						{{ __('book.only_finished_or_in_the_process_of_writing_books_are_allowed_to_be_sold') }}
					</div>
				@endif

				@if ($author->books()->whereReadyStatus('complete')->count() < 1)
					<div class="alert alert-warning">
						{{ __('book.for_the_sale_of_an_unfinished_book_you_must_have_at_least_one_completed_book_added') }}
					</div>
				@endif

				@if (empty($book->cover))
					<div class="alert alert-warning">
						{{ __('book.book_must_have_a_cover_for_sale') }}
					</div>
				@endif

				@if ((empty($book->annotation) or $book->annotation->character_count < config('litlife.min_annotation_characters_count_for_sale')))
					<div class="alert alert-warning">
						{{ __('book.annotation_must_contain_at_least_characters_for_sale', ['characters_count' => config('litlife.min_annotation_characters_count_for_sale')]) }}
					</div>
				@endif

				@if (!$book->isUserCreator(auth()->user()))
					<div class="alert alert-warning">
						{{ __('book.book_added_by_another_user') }}
					</div>
				@endif

				@if ($book->characters_count < config('litlife.minimum_characters_count_before_book_can_be_sold'))
					<div class="alert alert-warning">
						{{ __('book.minimum_characters_count_before_book_can_be_sold', ['characters_count' => config('litlife.minimum_characters_count_before_book_can_be_sold')]) }}
					</div>
				@endif
			@else
				@if ($book->free_sections_count >= $book->sections_count)
					<div class="alert alert-warning">
						{{ __('book.the_book_will_not_be_sold_if_the_number_of_free_chapters_is_greater_than_or_equal_to_the_number_of_chapters', ['sections_count' => $book->sections_count]) }}
					</div>
				@endif

				@if ($book->isPagesNewFormat())
					@if ($freeFragmentCharactersPercentage < config('litlife.recommended_minimum_free_fragment_as_a_percentage'))
						<div class="alert alert-warning">
							{{ __('book.we_recommend_increasing_the_free_fragment', ['percentage' => $freeFragmentCharactersPercentage,
							'recommended_minimum_free_fragment_as_a_percentage' => config('litlife.recommended_minimum_free_fragment_as_a_percentage')]) }}
						</div>
					@endif
				@endif

				@if ($book->isPrivate())
					<div class="mb-3">
						<div class="alert alert-warning">
							{{ __('book.for_the_book_to_start_being_sold_you_must_publish_it') }}
						</div>

						<a class="btn btn-primary"
						   href="{{ route('books.publish', ['book' => $book->id]) }}">{{ __('book.publish_a_book') }}</a>
					</div>
				@endif
			@endif

			<div class="card">
				<div class="card-body">

					<form class="mb-3" role="form" action="{{ route('books.sales.save', ['book' => $book]) }}"
						  method="post" enctype="multipart/form-data">

						@csrf

						<div class="row form-group">
							<label for="price" class="col-md-3 col-lg-2 col-form-label">{{ __('book.price') }}</label>
							<div class="col-md-9 col-lg-10">

								<input id="price" name="price"
									   class="form-control{{ $errors->has('price') ? ' is-invalid' : '' }}" type="text"
									   value="{{ old('price') ?: $book->price }}"/>

								<small id="price_help_block" class="form-text text-muted">
									{!! __('book.price_helper', ['min_price' => config('litlife.min_book_price'), 'max_price' => config('litlife.max_book_price')]) !!}
									<br/>

									@if (!empty(config('litlife.book_price_update_cooldown')))
										<strong>
											{!! __('book.price_change_cooldown_warning_helper', ['days_count' => config('litlife.book_price_update_cooldown')]) !!}
											<br/>
										</strong>
									@endif
								</small>
							</div>
						</div>

						@if ($book->isPagesNewFormat())
							<div class="row form-group">
								<label for="free_sections_count"
									   class="col-md-3 col-lg-2 col-form-label">{{ __('book.free_sections_count') }}</label>
								<div class="col-md-9 col-lg-10">


									<input id="free_sections_count" name="free_sections_count"
										   class="form-control{{ $errors->has('free_sections_count') ? ' is-invalid' : '' }}"
										   type="text"
										   value="{{ old('free_sections_count') ?: $book->free_sections_count }}"/>

									<small id="price_help_block" class="form-text text-muted">
										{!! __('book.free_sections_count_helper') !!}<br/>
										@if ($book->isForSale())
											<strong>{{ __('book.the_free_fragment_is_percentage', ['percentage' => $freeFragmentCharactersPercentage]) }}</strong>
										@endif
									</small>
								</div>
							</div>
						@else
							<input name="free_sections_count" type="hidden" value="0"/>
						@endif

						<div class="row form-group">
							<div class="col-md-9 col-lg-10 offset-md-3 offset-lg-2">
								<button type="submit" class="btn btn-primary">
									{{ __('common.save') }}
								</button>
							</div>
						</div>

					</form>

					@if ($book->isForSale() and $book->bought_times_count > 0)

						<div class="row form-group">
							<div class="col-md-9 col-lg-10 offset-md-3 offset-lg-2">
								<button
										class="remove_from_sale btn btn-outline-danger btn-sm">{{ __('book.remove_from_sale') }}</button>
							</div>
						</div>


						@push('scripts')
							<script type="text/javascript">
								(function () {
									$('.remove_from_sale').click(function () {

										bootbox.confirm({
											message: "{{ mb_ucfirst(__('common.warning')) }}! {{ __('book.remove_from_sale_warning', ['days' => config('litlife.book_removed_from_sale_cooldown_in_days')]) }}",
											buttons: {
												confirm: {
													label: '{{ __('common.i_confirm') }}',
													className: 'btn-danger'
												},
												cancel: {
													label: '{{ __('common.cancel') }}',
													className: 'btn-success'
												}
											},
											callback: function (result) {

												if (result === true) {
													location.href = '{{ route('books.remove_from_sale', ['book' => $book]) }}';
												}
											}
										});

									});
								})();
							</script>
						@endpush
					@endif

				</div>
			</div>
		@endif
	@endif
@endsection
