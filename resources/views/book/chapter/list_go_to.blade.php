@if (!empty($sections))

	<ul class="list-group list-group-flush">
		@foreach ($sections as $section)
			<a class="list-group-item d-flex justify-content-between align-items-center"
			   style="margin-left: {{ $section->depth * 20 }}px"
			   href="{{ route('books.sections.show', ['book' => $book, 'section' => $section->inner_id]) }}">

				{{ $section['title'] }}

				@if ($section->pages->first())
					<span>{{ $section->pages->first()->book_page }}</span>
				@endif
			</a>

			@if ($section->children->count() > 0)
				@include('book.chapter.list_go_to', ['sections' => $section->children])
			@endif
		@endforeach
	</ul>
@endif