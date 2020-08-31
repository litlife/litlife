<div class="row mb-3">
	<div class="col-12">

		<ul class="nav nav-pills">
			<li class="nav-item">
				<a href="{{ route('books.create') }}" class="nav-link {{ isActiveRoute('books.create') }}">
					{{ __('book.file_upload') }}
				</a>
			</li>
			<li class="nav-item">
				@if (empty($book))
					<a class="nav-link disabled">
						{{ __('book.filling_in_the_description') }}
					</a>
				@else
					<a href="{{ route('books.create.description', $book) }}"
					   class="nav-link {{ isActiveRoute('books.create.description') }}">
						{{ __('book.filling_in_the_description') }}
					</a>
				@endif
			</li>
			<li class="nav-item">
				@if (empty($book) or isActiveRoute('books.create.description'))
					<a class="nav-link disabled">
						{{ __('book.completion_of_the_addition') }}
					</a>
				@else
					<a href="{{ route('books.create.complete', $book) }}"
					   class="nav-link {{ isActiveRoute('books.create.complete') }}">
						{{ __('book.completion_of_the_addition') }}
					</a>
				@endif
			</li>
		</ul>

	</div>
</div>