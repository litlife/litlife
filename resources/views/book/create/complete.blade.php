@extends('layouts.app')

@push('scripts')

@endpush

@section('content')

	@include ('book.create.tab')

	<div class="alert alert-success">
		{{ __('book.the_book_was_added_successfully') }}
	</div>

	<div class="card">
		<div class="card-body">
			{{ __('book.now_you_can_publish_the_book_or_leave_it_in_your_personal_library') }}
			<ul>
				<li>{{ __('book.published_books_are_visible_to_all_users') }}</li>
				<li>{{ __('book.while_the_book_is_in_your_personal_library_only_you_can_see_it') }}</li>
				<li>{{ __('book.books_authors_and_series_in_your_personal_library_are_marked_with_the_icon') }}
					<i class="fas fa-lock"></i>
				</li>
				<li>{{ __('book.the_book_can_always_be_published_later') }}</li>
				<li>{{ __('book.you_can_find_the_added_books_in_the_menu') }}</li>
				<li>{{ __('book.to_go_back_to_editing_the_description_go_to_the_book_page_and_click_this_icon') }}
					<i class="fas fa-ellipsis-h"></i>
					{{ __('book.then_click_on_edit') }}</li>
			</ul>

			<div class="btn-margin-bottom-1">

				@can ('publish', $book)
					<a href="{{ route('books.publish', $book) }}" class="btn btn-primary">
						{{ __('book.publish') }}
					</a>
				@endcan

				<a href="{{ route('books.show', $book) }}" class="btn btn-primary">
					{{ __('book.go_to_the_book_page') }}
				</a>

				@if ($book->sections_count > 0)
					<a href="{{ route('books.sections.index', $book) }}" class="btn btn-primary">
						{{ __('book.go_to_editing_the_text_of_the_book') }}
					</a>
				@endif

				@can ('create', \App\Book::class)
					<a href="{{ route('books.create') }}" class="btn btn-primary">
						{{ __('book.add_another_book') }}
					</a>
				@endcan

				@if (optional($book->getManagerAssociatedWithUser(auth()->user()))->character == 'author')
					<a href="{{ route('books.sales.edit', $book) }}" class="btn btn-primary">
						{{ __('book.add_for_sale') }}
					</a>
				@endif
			</div>
		</div>
	</div>

@endsection
