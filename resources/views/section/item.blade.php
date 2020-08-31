<li class="list-group-item section py-1 pl-3 pr-0" data-id="{{ $item->id }}" data-inner-id="{{ $item->inner_id }}"
	data-book-id="{{ $item->book_id }}"
	data-name="section_{{ $item->id }}">
	<div class="d-flex" style="min-height:2.5rem;">
		<div class="flex-grow-1 mr-2">

			<div class="d-flex flex-column">

				@if ($item->isSection())
					<a href="{{ route('books.sections.show', ['book' => $book, 'section' => $item->inner_id]) }}"
					   class="title mt-2 ">
						<h6 class="mb-0 ">{{ $item->title }}</h6>
					</a>
				@elseif ($item->isNote())
					<a href="{{ route('books.notes.show', ['book' => $book, 'note' => $item->inner_id]) }}"
					   class="title mt-2 ">
						<h6 class="mb-0 ">{{ $item->title }}</h6>
					</a>
				@endif

				<div class="p-0">
					<small class="text-muted">

						@if ($book->isForSale() and empty($purchase))
							@if ($item->isPaid())
								<a class="text-secondary"
								   href="{{ route('books.purchase', $book) }}">{{ __('section.available_for_a_fee') }}</a>
							@else
								{{ __('section.available_for_free') }}
							@endif
						@endif

						@if (isset($item->character_count))
							<span style="font-weight: normal">{{ __('section.character_count') }}: {{ $item->character_count }} </span>
						@endif

						@if (!empty($item->user_edited_at))
							<span style="font-weight: normal">{{ __('section.user_edited_at') }} <x-time :time="$item->user_edited_at"/> </span>
						@endif

						@if ($item->isPrivate())
							{{ __('section.status_array.private') }}
						@endif
					</small>
				</div>
			</div>

		</div>

		<div class="flex-shrink-1 mr-3">

			<div class="btn-group dropdown" data-toggle="tooltip" data-placement="top"
				 title="{{ __('common.open_actions') }}">
				<button class="btn btn-light dropdown-toggle" type="button"
						id="dropdownMenuButton_{{ $item->id }}"
						data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					<i class="fas fa-ellipsis-h"></i>
				</button>
				<div class="dropdown-menu dropdown-menu-right" role="menu"
					 aria-labelledby="dropdownMenuButton_{{ $item->id }}">

					@can ('save_sections_position', $book)
						<span class="dropdown-item text-lowercase handle pointer">
                            <i class="fas fa-arrows-alt"></i> {{ __('common.move') }}
                        </span>
					@endcan

					@can('create_section', $item->book)
						@if ($item->isSection())
							<a class="dropdown-item text-lowercase"
							   href="{{ route('books.sections.create', ['book' => $book, 'parent' => $item->inner_id]) }}">
								{{ __('section.create_subsection') }}
							</a>
						@endif
					@endcan

					@can ('update', $item)
						<a class="dropdown-item text-lowercase"
						   href="{{ route('books.sections.edit', ['book' => $book, 'section' => $item->inner_id]) }}">
							{{ __('common.edit') }}
						</a>
					@endcan

					<a class="delete pointer dropdown-item text-lowercase" href="javascript:void(0)" disabled="disabled"
					   data-loading-text="{{ __('common.deleting') }}..."
					   @cannot ('delete', $item) style="display:none;"@endcannot>
						{{ __('common.delete') }}
					</a>

					<a class="restore pointer dropdown-item text-lowercase" href="javascript:void(0)"
					   disabled="disabled"
					   data-loading-text="{{ __('common.restoring') }}"
					   @cannot ('restore', $item) style="display:none;"@endcannot>
						{{ __('common.restore') }}
					</a>

					@if ($item->isSection())
						@can ('move_sections_to_notes', $book)
							<span class="dropdown-item text-lowercase move-to-notes pointer">
                            {{ __('section.move_to_the_notes') }}
                        </span>
						@endcan
					@elseif ($item->isNote())
						@can ('move_sections_to_notes', $book)
							<span class="dropdown-item text-lowercase move-to-chapters pointer">
                            {{ __('section.move_to_the_chapters') }}
                        </span>
						@endcan
					@endif

				</div>
			</div>
		</div>
	</div>
	<ol class="border-left ml-2">
		@if(count($item->children) > 0)

			@foreach($item->children as $section)
				@include('section.item', ['item' => $section])
			@endforeach

		@endif
	</ol>
</li>

