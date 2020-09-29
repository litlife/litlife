@extends('layouts.app')

@push('scripts')

	<script src="{{ mix('js/books.old.page.js', config('litlife.assets_path')) }}"></script>

@endpush

@push('css')
	<link href="{{ mix('css/bootstrap-colorpicker.css', config('litlife.assets_path')) }}" rel="stylesheet">
@endpush

@section('content')

	<div class="row mb-3">
		<div class="col-12 btn-margin-bottom-1">

			@include('book.chapter.button', ['chapters_count' => $sections_count, 'book' => $book])

			<a href="{{ route('settings.read_style') }}" class="btn btn-light change_read_style" target="_blank">
				<i class="fas fa-cog"></i> {{ __('common.change_read_style') }}
			</a>

		</div>
	</div>


	@if ($pages->hasPages())
		{{ $pages->appends(request()->except(['page', 'ajax']))->links('vendor.pagination.bootstrap-4_with_current_page') }}
	@endif

	@include('read_style_css')

	<div class="card mb-3">
		<div class="book_text card-body imgs-fluid @if ($book->copy_protection) noselect @endif p-sm-4 p-md-5">

			{!! $before !!}

			@can ('see_ads', \App\User::class)
				@can('display_ads', $book)
					<x-ad-block name="read_online"/>
				@endcan
			@endcan

			{!! $after !!}
		</div>
	</div>

	<div class="row d-flex">
		<div class="order-md-0 order-1 col-12 col-md-6 text-center pr-md-0">

			@if (!empty($pages->previousPageUrl()))
				<a class="btn btn-light btn-block text-truncate"
				   href="{{ $pages->previousPageUrl() }}">
					{{ __('page.previous') }}
				</a>
			@endif
		</div>
		<div class="order-md-1 order-0 col-12 col-md-6 text-center mb-2 mb-md-0">
			@if (!empty($pages->nextPageUrl()))
				<a class="btn btn-primary btn-block text-truncate"
				   href="{{ $pages->nextPageUrl() }}">
					{{ __('page.next') }}
				</a>
			@endif

			@if (!empty($pages->lastPage()) and $pages->currentPage() == $pages->lastPage())
				<a class="btn btn-primary btn-block text-truncate"
				   href="{{ route('books.read_status.store', ['book' => $book, 'code' => 'readed']) }}">
					{{ __('book.update_read_status_as_book_complete_and_go_to_book_page') }}
				</a>
			@endif
		</div>
	</div>

	@include('book.age_access_modal')

@endsection
