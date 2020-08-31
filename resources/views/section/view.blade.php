@extends('layouts.app')

@push('scripts')

	<script src="{{ mix('js/books.sections.show.js', config('litlife.assets_path')) }}"></script>

@endpush

@push('css')
	<link href="{{ mix('css/bootstrap-colorpicker.css', config('litlife.assets_path')) }}" rel="stylesheet">
@endpush

@section('content')

	@if ($section->trashed())
		<div class="row mb-3">
			<div class="col-12 btn-margin-bottom-1">

				<h5>{{ __('section.deleted') }}</h5>

				@can('restore', $section)
					<a href="{{ route('books.sections.delete', ['book' => $book, 'section' => $section->inner_id]) }}"
					   class="btn btn-light">
						{{ __('common.restore') }}
					</a>
				@endcan

			</div>
		</div>
	@else

		@include('book.age_access_modal')

		<div class="mb-3">
			@can('update', $section)
				<a href="{{ route('books.sections.edit', ['book' => $book, 'section' => $section->inner_id]) }}"
				   class="btn btn-light">
					<i class="far fa-edit"></i> {{ __('common.edit') }}
				</a>
			@endcan

			<a href="{{ route('settings.read_style') }}" class="btn btn-light change_read_style" target="_blank">
				<i class="fas fa-cog"></i> {{ __('common.change_read_style') }}
			</a>
		</div>

		@isset($book_pages)
			@if ($book_pages->hasPages())
				<div class="text-center mt-4">
					{{ $book_pages->appends(request()->except(['page', 'ajax']))->links('vendor.pagination.bootstrap-4_with_current_page') }}
				</div>
			@endif
		@endisset

		<div class="card mb-3">
			<div class="card-body p-sm-4 p-md-5">
				<div class="imgs-fluid cke_editable">
					@yield('text')
				</div>
				@if ($pages->hasPages())
					<div class="text-center mt-4">
						{{ $pages->appends(request()->except(['page', 'ajax']))->links('vendor.pagination.bootstrap-4_without_current_page_and_per_page') }}
					</div>
				@endif
			</div>
		</div>

		@include('read_style_css')

		<div id="prev_next_navigation" class="row d-flex">
			<div class="order-md-0 order-1 col-12 col-md-6 text-center pr-md-0">

				@if (filled($prevSection))
					<a class="btn btn-light btn-block text-truncate" rel="prev"
					   href="{{ $prevSectionLastPageUrl }}">
						{{ __('section.previous_section') }} "{{ $prevSection->title }}"
					</a>
				@elseif ($pages->currentPage() > 1)
					<a class="btn btn-light btn-block text-truncate" rel="prev"
					   href="{{ $pages->previousPageUrl() }}">
						{{ __('section.previous_page') }}
					</a>
				@endif

			</div>
			<div class="order-md-1 order-0 col-12 col-md-6 text-center mb-2 mb-md-0">
				@if (filled($nextSection))
					<a class="btn btn-primary btn-block text-truncate" rel="next"
					   href="{{ $nextSectionFirstPageUrl }}">
						{{ __('section.next_section') }} "{{ $nextSection->title }}"
					</a>
				@elseif ($pages->currentPage() != $pages->lastPage())
					<a class="btn btn-primary btn-block text-truncate" rel="next"
					   href="{{ $pages->nextPageUrl() }}">
						{{ __('section.next_page') }}
					</a>
				@endif

				@if (empty($nextSection) and !empty($pages->lastPage()) and $pages->currentPage() == $pages->lastPage())
					<a class="btn btn-primary btn-block text-truncate"
					   href="{{ route('books.read_status.store', ['book' => $book, 'code' => 'readed']) }}">
						{{ __('book.update_read_status_as_book_complete_and_go_to_book_page') }}
					</a>
				@endif

			</div>
		</div>

	@endif
@endsection