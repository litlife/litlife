<div class="d-inline-block">
	@if (isset($book))

		@if ($book->trashed() and empty($show_even_if_trashed))

			@if (empty($href_disable))
				<a class="d-inline-block" href="{{ route('books.show', $book) }}">
					@endif
					{{ __('book.deleted') }}
					@if (empty($href_disable))
				</a>
			@endif

		@elseif (!$book->isHaveAccess())

			@if (empty($href_disable))
				<a class="d-inline-block" href="{{ route('books.show', $book) }}">
					@endif
					{{ __('book.access_denied') }}
					@if (empty($href_disable))
				</a>
			@endif

		@else

			@if (empty($href_disable))
				<a class="d-inline-block" href="{{ route('books.show', $book) }}">
					@endif
					{{ $book->title }}
					@if (empty($href_disable))
				</a>
			@endif

			@if ($book->trashed())
				<span class="text-muted">({{ __('book.deleted') }})</span>
			@endif

			@if (empty($no_badges))

				@if ($book->is_collection)
					<span class="text-muted text-lowercase">({{ __('book.is_collection') }})</span>
				@endif

				@if ($book->is_si)
					<span class="text-muted" data-toggle="tooltip" data-placement="top" title="{{ __('book.is_si') }}">({{ __('book.si') }})</span>
				@endif

				@if ($book->is_lp)
					<span class="text-muted" data-toggle="tooltip" data-placement="top" title="{{ __('book.is_lp') }}">({{ __('book.lp') }})</span>
				@endif

				@if ($book->age)
					<sup>
						<span class="text-muted">{{ $book->age }}+</span>
					</sup>
				@endif

				@if ($book->isPrivate())
					<i class="fas fa-lock" data-toggle="tooltip" data-placement="top"
					   title="{{ __('book.private_tooltip') }}"></i>
				@endif

			@endif
		@endif

	@else
		<span class="book name">{{ __('book.deleted') }}</span>
	@endif
</div>

