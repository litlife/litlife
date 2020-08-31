<div class="row mb-3">
	<div class="col-12">

		<ul class="nav nav-pills">
			@can ('update', $book)
				<li class="nav-item">
					<a class="nav-link {{ isActiveRoute('books.edit') }}" href="{{ url('/books/'.$book->id.'/edit') }}">
						{{ __('book.description') }}
					</a>
				</li>
			@endcan

			<li class="nav-item">
				<a class="nav-link {{ isActiveRoute('books.sections.index') }}"
				   href="{{ url('/books/'.$book->id.'/sections') }}">
					{{ __('model.sections') }}
					<span class="badge">{{ $book->sections_count }}
						@if ($book->private_chapters_count > 0)
							@can ('create_section', $book)
								/ {{ $book->private_chapters_count }}
							@endcan
						@endif
                    </span>
				</a>
			</li>
			@if ($book->isPagesNewFormat())
				<li class="nav-item">
					<a class="nav-link {{ isActiveRoute('books.notes.index') }}" href="{{ url('/books/'.$book->id.'/notes') }}">
						{{ __('model.notes') }}
						<span class="badge">{{ $book->notes_count }}</span>
					</a>
				</li>
			@endif
			<li class="nav-item">
				<a class="nav-link {{ isActiveRoute('books.attachments.index') }}"
				   href="{{ url('/books/'.$book->id.'/attachments') }}">
					{{ __('model.attachments') }}
					<span class="badge">{{ $book->attachments_count }}</span>
				</a>
			</li>

			<li class="nav-item">
				<a class="nav-link {{ isActiveRoute('books.keywords.index') }}"
				   href="{{ url('/books/'.$book->id.'/keywords') }}">
					{{ trans_choice('keyword.keywords', 2) }}
				</a>
			</li>

			@can ('use_shop', \App\User::class)
				@can('author', $book)

					<li class="nav-item">
						<a class="nav-link {{ isActiveRoute('books.sales.edit') }}"
						   href="{{ url('/books/'.$book->id.'/sales') }}">
							{{ __('book.sales') }}

							@if ($book->isDisplaySaleWarning())
								<i class="fas fa-exclamation-triangle"></i>
							@endif
						</a>
					</li>
				@endcan
			@endcan

			@can('change_access', $book)
				<li class="nav-item">
					<a class="nav-link {{ isActiveRoute('books.access.edit') }}"
					   href="{{ url('/books/'.$book->id.'/access') }}">
						{{ __('book.access') }}
					</a>
				</li>
			@endcan

			@can('attachAward', $book)
				<li class="nav-item">
					<a class="nav-link {{ isActiveRoute('books.awards.index') }}"
					   href="{{ url('/books/'.$book->id.'/awards') }}">
						{{ trans_choice('award.awards', 2) }}
						<span class="badge">{{ $book->awards_count }}</span>
					</a>
				</li>
			@endcan
		</ul>
	</div>
</div>