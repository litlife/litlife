@can ('view_read_button', $book)

	@if (auth()->check() and $page = $book->remembered_pages->where('user_id', auth()->id())->first())

		<a class="btn btn-primary font-weight-bold" data-button="contune-reading"
		   data-toggle="tooltip" data-placement="top"
		   title="{{ $page ? __('book.continue_reading_tooltip', ['page' => $page->page]) : '' }}"
		   href="{{ route('books.read.online', $book) }}">
			{{ __('book.continue_reading') }}
		</a>

	@else

		<a href="{{ route('books.read.online', $book) }}"
		   data-button="reading" class="btn btn-primary font-weight-bold">
			{{ __('common.read_online') }}
		</a>

	@endif

@endcan

@can ('buy_button', $book)

	@auth
		<a href="{{ route('books.purchase', ['book' => $book]) }}"
		   class="btn btn-primary font-weight-bold">
			{{ trans_choice('book.buy_a_book', $book->price, ['price' => $book->price]) }}
		</a>
	@endauth

	@guest
		<button type="button" class="btn btn-primary font-weight-bold"
				data-container="body"
				data-toggle="popover" data-placement="top" data-html="true"
				data-content="{{ __('user.unauthenticated_error_description') }}">
			{{ trans_choice('book.buy_a_book', $book->price, ['price' => $book->price]) }}
		</button>
	@endguest

@endcan

@can('view_download_files', $book)
	@if (!empty($book->formats))
		<a href="{{ route('books.show', ['book' => $book]) }}#files" class="btn btn-outline-primary font-weight-bold">
			{{ __('common.download') }}
			@foreach ($book->formats as $format)
				{{ $format }}{{ $loop->last ? '' : ', ' }}
			@endforeach
		</a>
	@endif
@endcan