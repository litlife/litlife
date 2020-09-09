@if (!empty($sections))

	<ul class="list-group list-group-flush">
		@foreach ($sections as $section)
			<a class="list-group-item d-flex justify-content-between align-items-center"
			   style="margin-left: {{ (($section['level']-1) * 20) }}px"
			   href="{{ route('books.old.page', ['book' => $book->id, 'page' => $section['page']]) }}@if (!empty($section['sn']))#{{ @$section['sn'] }}@endif">
				{{ $section['title'] }}
				<span>{{ isset($section['page']) ? $section['page'] : '' }}</span>
			</a>
			@if (!empty($section['ch']))
				@include('book.page.section_list', ['sections' => $section['ch']])
			@endif
		@endforeach
	</ul>

@endif