<li class="file list-group-item pl-0 pr-0" data-id="{{ $file->id }}">
	<div class="d-flex">
		<div class="pr-2" itemprop="workExample">
			@if ($file->exists())
				@if (in_array($file->format, ['mp3', 'ogg']))
					<audio controls style="margin-right:10px">
						<source
								src="{{ route('books.files.show', ['book' => $file->book, 'fileName' => $file->encoded_name]) }}"
								type="audio/mpeg">
						{{ __('browser_audio_tag_not_supported') }}
					</audio>
				@else
					<a class="btn btn-outline-primary "
					   data-toggle="tooltip" data-placement="top" title="Скачать {{ $file->format }}"
					   href="{{ route('books.files.show', ['book' => $file->book, 'fileName' => $file->encoded_name]) }}">
						<i class="fas fa-download "></i>
						<strong>{{ $file->format }}</strong>
					</a>
				@endif
			@else
				{{ __('book_file.not_found', ['title' => $file->format]) }}
			@endif
		</div>

		<small class="text-muted">

			{{ __('book_file.size') }}: {{ ByteUnits\bytes($file->size)->format('MB') }}

			@can ('display_technical_information', \App\Book::class)
				@if ($file->isAutoCreated())
					{{ __('book_file.was_created_by_the_site') }}
				@else
					@if ($file->create_user)
						{{ trans_choice('user.created', $file->create_user->gender) }}
						<x-user-name :user="$file->create_user"/>
					@endif
				@endif
			@endcan

			{{ __('book_file.created_at') }}
			<x-time :time="$file->created_at"/>

			<br/>

			@if (!empty($file->number))
				{{ __('book_file.number') }}: #{{ $file->number }}
			@endif

			@if (!empty($file->comment))
				<span itemprop="description">{{ $file->comment }}</span>
			@endif

			@if ($file->isSource())
				{{ __('book_file.source') }}
			@endif
		</small>

		<div class="ml-auto">
			<div class="btn-group" data-toggle="tooltip" data-placement="top" title="{{ __('common.open_actions') }}">
				<button class="btn btn-light dropdown-toggle" type="button" id="bookFileDropdownButton_{{ $file->id }}"
						data-toggle="dropdown"
						aria-haspopup="true"
						aria-expanded="false">
					<i class="fas fa-ellipsis-h"></i>
				</button>
				<div class="dropdown-menu dropdown-menu-right" aria-labelledby="bookFileDropdownButton_{{ $file->id }}">

					@can ('update', $file)
						<a class="dropdown-item text-lowercase"
						   href="{{ route('books.files.edit', compact('book', 'file')) }}">
							{{ __('common.edit') }}
						</a>
					@endcan

					<a class="delete pointer dropdown-item text-lowercase" disabled="disabled"
					   data-loading-text="{{ __('common.deleting') }}..."
					   @cannot ('delete', $file) style="display:none;"@endcannot>
						{{ __('common.delete') }}
					</a>

					<a class="restore pointer dropdown-item text-lowercase" disabled="disabled"
					   data-loading-text="{{ __('common.restoring') }}..."
					   @cannot ('restore', $file) style="display:none;"@endcannot>
						{{ __('common.restore') }}
					</a>

					@can ('set_source_and_make_pages', $file)
						<a class="dropdown-item text-lowercase" data-toggle="tooltip" data-placement="top"
						   title="{{ __('book_file.set_as_source_description') }}"
						   href="{{ route('book_files.set_source_and_make_pages', compact('file')) }}">
							{{ __('book_file.set_as_source') }}
						</a>
					@endcan

				</div>
			</div>
		</div>
	</div>
</li>
