@extends('layouts.app')

@push('scripts')

	@if ($type == 'section')
		<script src="{{ mix('js/books.sections.index.js', config('litlife.assets_path')) }}"
				type="text/javascript"></script>
	@elseif ($type == 'note')
		<script src="{{ mix('js/books.notes.index.js', config('litlife.assets_path')) }}"
				type="text/javascript"></script>
	@endif

@endpush

@push('css')

	<link href="{{ mix('css/sections-list.css', config('litlife.assets_path')) }}" rel="stylesheet"/>

@endpush

@section('content')

	@include('scripts.jquery-sortable')
	@include ('book.edit_tab')

	@if (!$book->isPagesNewFormat())
		@include('book.new_pages_format_warning')
	@else
		<div class="row">
			<div class="col-12">

				@if(count($sections) > 0)
					<ol class="selectable sortable list-group list-group-flush mb-3">
						@foreach($sections as $number => $section)
							@include('section.item', ['item' => $section, 'number' => $number])
						@endforeach
					</ol>
				@else
					@if ($type == 'section')
						<p class="alert alert-info" style="padding:10px">
							{{ __('section.nothing_found') }}
						</p>
					@elseif ($type == 'note')
						<p class="alert alert-info" style="padding:10px">
							{{ __('note.nothing_found') }}
						</p>
					@endif
				@endif

			</div>
		</div>

		<div class="row">
			<div class="col-12">

				@if ($type == 'section')

					@can ('create_section', $book)
						<a class="btn btn-primary" href="{{ route('books.sections.create', ['book' => $book]) }}">
							{{ __('section.add_new_chapter') }}
						</a>
					@endcan

					@can ('save_sections_position', $book)
						@if(count($sections) > 0)
							<button class="save btn btn-light">
								<i class="fas fa-spinner fa-spin"
								   style="display: none;"></i> {{ __('section.save_position') }}
							</button>
						@endif
					@endcan

				@elseif($type == 'note')

					@can ('create_section', $book)
						<a class="btn btn-primary" href="{{ route('books.notes.create', ['book' => $book]) }}">
							{{ __('section.add_new_note') }}
						</a>
					@endcan

					@can ('save_sections_position', $book)
						@if(count($sections) > 0)
							<button class="save btn btn-light">
								<i class="fas fa-spinner fa-spin"
								   style="display: none;"></i> {{ __('note.save_position') }}
							</button>
						@endif
					@endcan

				@endif
			</div>
		</div>

		@include('book.age_access_modal')
	@endif

@endsection
