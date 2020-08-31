@foreach($notes as $section)

	<li class="section list-group-item" data-id="{{ $section->id }}" data-book-id="{{ $item->book_id }}"
		data-inner-id="{{ $item->inner_id }}"
		data-name="section_{{ $section->id }}">
		<div class="section_block">

			@can ('update', $book)

				<div class="dropdown inline" data-toggle="tooltip" data-placement="top"
					 title="{{ __('common.open_actions') }}">
					<button class="btn btn-secondary dropdown-toggle" type="button"
							id="dropdownMenuButton_{{ $section->id }}"
							data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					</button>
					<div class="dropdown-menu dropdown-menu-right" role="menu"
						 aria-labelledby="dropdownMenuButton_{{ $section->id }}">
						<a class="dropdown-item text-lowercase"
						   href="{{ route('books.sections.edit', ['book' => $book, 'section' => $section->inner_id]) }}">
							{{ __('common.edit') }}
						</a>
						<a class="dropdown-item text-lowercase"
						   href="{{ route('books.sections.destroy', ['book' => $book, 'section' => $section->inner_id]) }}">
							{{ __('common.delete') }}
						</a>
					</div>
				</div>

				<span class="handle btn btn-light  fas fa-arrows-alt-v"></span>

				<input class="select" type="checkbox">

			@endcan

			<a href="{{ route('books.sections.show', ['book' => $book, 'section' => $section->inner_id]) }}"
			   style="font-weight: bold;">{{ $section->title }}</a>

			@if (isset($section->character_count))
				<span style="font-weight: normal">{{ __('note.character_count') }}: {{ $section->character_count }} </span>
			@endif

			@if (!empty($section->created_at))
				<span style="font-weight: normal">{{ __('note.created_at') }} <x-time :time="$section->created_at"/> </span>
			@endif

			@if (!empty($section->user_edited_at))
				<span style="font-weight: normal">{{ __('note.user_edited_at') }} <x-time :time="$section->user_edited_at"/> </span>
			@endif

		</div>
		<ol>
			@if(count($section->children) > 0)
				@include('section.item', ['sections' => $section->children])
			@endif
		</ol>
	</li>

@endforeach